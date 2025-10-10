<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer installato con Composer

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- Sanitizzazione e validazione ---
    $nome = isset($_POST['nome']) ? strip_tags(trim($_POST['nome'])) : '';
    $cognome = isset($_POST['cognome']) ? strip_tags(trim($_POST['cognome'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $messaggio = isset($_POST['messaggio']) ? strip_tags(trim($_POST['messaggio'])) : '';

    if (empty($nome) || empty($cognome) || empty($email) || empty($messaggio)) {
        $response['message'] = 'Tutti i campi sono obbligatori.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Indirizzo email non valido.';
        echo json_encode($response);
        exit;
    }

    // --- Corpo email ---
    $subject = "Nuovo contatto da 3DTERMOEDIL - $nome $cognome";
    $body = "
    <h2>Nuovo Messaggio dal Sito Web</h2>
    <p><strong>Nome:</strong> $nome</p>
    <p><strong>Cognome:</strong> $cognome</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Messaggio:</strong><br>" . nl2br(htmlspecialchars($messaggio)) . "</p>
    <hr>
    <p style='font-size:12px;color:#555'>Inviato il " . date('d/m/Y H:i:s') . "</p>";

    // --- Invio tramite Gmail SMTP ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mailatelisei27@gmail.com'; // la tua Gmail
        $mail->Password = 'tjiq rhti dojc tyyv';      // App password generata da Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('mailatelisei27@gmail.com', '3DTERMOEDIL');
        $mail->addAddress('3dtermoedil@gmail.com', '3D Termoedil');
        $mail->addReplyTo($email, "$nome $cognome");

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        $response['success'] = true;
        $response['message'] = 'Messaggio inviato con successo!';
    } catch (Exception $e) {
        $response['message'] = "Errore nell'invio: {$mail->ErrorInfo}";
    }

} else {
    $response['message'] = 'Metodo di richiesta non valido.';
}

echo json_encode($response);
?>
