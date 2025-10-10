<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Carica variabili d'ambiente dal file .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

// Risposta JSON
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Controlla che sia POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Metodo di richiesta non valido.';
    echo json_encode($response);
    exit;
}

// Sanitizza e valida dati
$nome = isset($_POST['nome']) ? strip_tags(trim($_POST['nome'])) : '';
$cognome = isset($_POST['cognome']) ? strip_tags(trim($_POST['cognome'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$messaggio = isset($_POST['messaggio']) ? strip_tags(trim($_POST['messaggio'])) : '';

if (!$nome || !$cognome || !$email || !$messaggio) {
    $response['message'] = 'Tutti i campi sono obbligatori.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Indirizzo email non valido.';
    echo json_encode($response);
    exit;
}

// Protezione spam base (sessione)
session_start();
if (!isset($_SESSION['form_submissions'])) $_SESSION['form_submissions'] = 0;
if ($_SESSION['form_submissions'] >= 3) {
    $response['message'] = 'Hai raggiunto il limite di invii.';
    echo json_encode($response);
    exit;
}

try {
    $mail = new PHPMailer(true);

    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('GMAIL_USER');       // Legge dal file .env
    $mail->Password   = getenv('GMAIL_APP_PASSWORD');
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Destinatario
    $mail->setFrom(getenv('GMAIL_USER'), '3D Termoedil');
    $mail->addAddress(getenv('GMAIL_USER'), 'Destinatario');

    // Reply-To
    $mail->addReplyTo($email, "$nome $cognome");

    // Contenuto email
    $mail->isHTML(true);
    $mail->Subject = "Nuovo contatto da 3DTERMOEDIL - $nome $cognome";
    $mail->Body    = "
        <h2>Nuovo messaggio dal sito web</h2>
        <p><strong>Nome:</strong> $nome</p>
        <p><strong>Cognome:</strong> $cognome</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Messaggio:</strong><br>" . nl2br(htmlspecialchars($messaggio)) . "</p>
        <p>Data: " . date('d/m/Y H:i:s') . "</p>
    ";

    $mail->send();
    $_SESSION['form_submissions']++;

    // Email di conferma al mittente
    $mailConfirm = new PHPMailer(true);
    $mailConfirm->isSMTP();
    $mailConfirm->Host       = 'smtp.gmail.com';
    $mailConfirm->SMTPAuth   = true;
    $mailConfirm->Username   = getenv('GMAIL_USER');
    $mailConfirm->Password   = getenv('GMAIL_APP_PASSWORD');
    $mailConfirm->SMTPSecure = 'tls';
    $mailConfirm->Port       = 587;

    $mailConfirm->setFrom(getenv('GMAIL_USER'), '3D Termoedil');
    $mailConfirm->addAddress($email, "$nome $cognome");
    $mailConfirm->isHTML(true);
    $mailConfirm->Subject = "Conferma ricezione messaggio - 3DTERMOEDIL";
    $mailConfirm->Body = "
        <h2>Grazie per averci contattato!</h2>
        <p>Gentile $nome $cognome,</p>
        <p>Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.</p>
        <p><strong>3D Termoedil</strong></p>
    ";
    $mailConfirm->send();

    $response['success'] = true;
    $response['message'] = 'Messaggio inviato con successo!';
} catch (Exception $e) {
    $response['message'] = "Errore nell'invio: {$mail->ErrorInfo}";
}

echo json_encode($response);
