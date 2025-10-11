<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');


require 'vendor/autoload.php';

// === CARICAMENTO FILE .env === //
$envFile = __DIR__ . '/admin_data.env';

if (!file_exists($envFile)) {
    echo "⚠️ ERRORE: File .env non trovato in: $envFile";
    exit;
}

// Legge il contenuto riga per riga
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue; // ignora commenti
    putenv(trim($line)); // carica variabile d’ambiente
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

// Sanitizza input
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

// Protezione spam base
session_start();
if (!isset($_SESSION['form_submissions'])) $_SESSION['form_submissions'] = 0;
if ($_SESSION['form_submissions'] >= 100) {
    $response['message'] = 'Hai raggiunto il limite di invii.';
    echo json_encode($response);
    exit;
}

try {
    // === CONFIGURAZIONE MAIL PRINCIPALE ===
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    /*$mail->SMTPDebug = 0;          // Debug SMTP attivo
    $mail->Debugoutput = 'html';   // Mostra log in formato leggibile*/
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('GMAIL_USER');
    $mail->Password = getenv('GMAIL_APP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // invece di 'tls'
    $mail->Port       = 587;

    $mail->setFrom(getenv('GMAIL_USER'), '3D Termoedil');
    $mail->addAddress(getenv('GMAIL_USER'), '3D Termoedil');
    $mail->addReplyTo($email, "$nome $cognome");

    $mail->isHTML(true);
    $mail->Subject = "Nuovo contatto da 3DTERMOEDIL - $nome $cognome";
    $mail->Body = "
        <h2>Nuovo messaggio dal sito</h2>
        <p><strong>Nome:</strong> $nome</p>
        <p><strong>Cognome:</strong> $cognome</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Messaggio:</strong><br>" . nl2br(htmlspecialchars($messaggio)) . "</p>
        <p>Data: " . date('d/m/Y H:i:s') . "</p>
    ";

    $mail->send();
    $_SESSION['form_submissions']++;

    // === MAIL DI CONFERMA AL MITTENTE ===
    $mailConfirm = new PHPMailer(true);
    $mailConfirm->isSMTP();
    $mailConfirm->Host = 'smtp.gmail.com';
    $mailConfirm->SMTPAuth = true;
    $mailConfirm->Username = getenv('GMAIL_USER');
    $mailConfirm->Password = getenv('GMAIL_APP_PASSWORD');
    $mailConfirm->SMTPSecure = 'tls';
    $mailConfirm->Port = 587;

    $mailConfirm->setFrom(getenv('GMAIL_USER'), '3D Termoedil');
    $mailConfirm->addAddress($email, "$nome $cognome");
    $mailConfirm->isHTML(true);
    $mailConfirm->Subject = "Conferma ricezione - 3DTERMOEDIL";
    $mailConfirm->Body = "
        <h2>Grazie per averci contattato!</h2>
        <p>Buongiorno $nome $cognome,</p>
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
