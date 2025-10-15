<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Caricamento .env
$envFile = __DIR__ . '/../admin_data.env';
if (!file_exists($envFile)) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>"⚠️ ERRORE: File .env non trovato"]);
    exit;
}
foreach(file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
    if(str_starts_with(trim($line),'#')) continue;
    putenv(trim($line));
}

// Risposta JSON
header('Content-Type: application/json');
$response = ['success'=>false, 'message'=>''];

// Verifica metodo POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Metodo di richiesta non valido.']);
    exit;
}

// Sanitizzazione input e rimozione CRLF per header mail
function sanitize_header($s){ return str_replace(["\r","\n"],' ', strip_tags(trim($s))); }

$nome = sanitize_header($_POST['nome'] ?? '');
$cognome = sanitize_header($_POST['cognome'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$messaggio = strip_tags(trim($_POST['messaggio'] ?? ''));

if(!$nome || !$cognome || !$email || !$messaggio){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Tutti i campi sono obbligatori.']);
    exit;
}

// Protezione spam semplice
session_start();
if(!isset($_SESSION['form_submissions'])) $_SESSION['form_submissions'] = 0;
if($_SESSION['form_submissions'] >= 100){
    http_response_code(429);
    echo json_encode(['success'=>false,'message'=>'Hai raggiunto il limite di invii.']);
    exit;
}

try {
    // Email al sito
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('GMAIL_USER');
    $mail->Password = getenv('GMAIL_APP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

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

    // Email di conferma
    $mailConfirm = new PHPMailer(true);
    $mailConfirm->isSMTP();
    $mailConfirm->Host = 'smtp.gmail.com';
    $mailConfirm->SMTPAuth = true;
    $mailConfirm->Username = getenv('GMAIL_USER');
    $mailConfirm->Password = getenv('GMAIL_APP_PASSWORD');
    $mailConfirm->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
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
} catch (Exception $e){
    $response['message'] = "Errore nell'invio: {$mail->ErrorInfo}";
}

echo json_encode($response);
