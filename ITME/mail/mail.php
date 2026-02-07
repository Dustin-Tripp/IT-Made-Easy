<?php
// Check if reCAPTCHA response is set
if (!isset($_POST['g-recaptcha-response'])) {
  echo "No CAPTCHA response received.";
  exit;
}

$recaptcha_response = $_POST['g-recaptcha-response'];
$secret = '6LfBr7EpAAAAANnyeb0ZuM1sZhPdwi8KPY_uTIp3'; // Your reCAPTCHA secret key

// Verify the reCAPTCHA response
$verifyResponse = file_get_contents(
  "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secret) .
  "&response=" . urlencode($recaptcha_response) .
  "&remoteip=" . urlencode($_SERVER['REMOTE_ADDR'] ?? '')
);

$responseData = json_decode($verifyResponse);
if (empty($responseData->success)) {
  echo "CAPTCHA verification failed. Please try again.";
  exit;
}

// Honeypot spam check - if this field is filled, it's likely a bot
if (!empty($_POST['website'])) {
  header("location: ../mail-success.html");
  exit;
}

// Optional: time-to-submit spam check (works if you add form_time hidden field)
$minMs = 2500;
$formTime = isset($_POST['form_time']) ? (int)$_POST['form_time'] : 0;
$nowMs = (int) round(microtime(true) * 1000);
if ($formTime > 0 && ($nowMs - $formTime) < $minMs) {
  header("location: ../mail-success.html");
  exit;
}

// Helper to prevent header injection / weird formatting
function clean_text($v) {
  $v = trim((string)$v);
  return str_replace(["\r", "\n"], " ", $v);
}

$name  = clean_text($_POST['name'] ?? '');
$email = clean_text($_POST['email'] ?? '');
$phone = clean_text($_POST['phone'] ?? '');
$company = clean_text($_POST['company'] ?? '');
$issue = clean_text($_POST['issue'] ?? ($_POST['priority'] ?? ''));
$preferred_contact = clean_text($_POST['preferred_contact'] ?? '');
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $issue === '' || $message === '') {
  echo "Please fill out the required fields.";
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "Please enter a valid email address.";
  exit;
}

$subject = "New ITME Risk Check Request ($issue) - $name";

$email_message =
"New website inquiry:\n\n" .
"Name: $name\n" .
"Email: $email\n" .
($phone !== '' ? "Phone: $phone\n" : "") .
($company !== '' ? "Company: $company\n" : "") .
"Issue: $issue\n" .
($preferred_contact !== '' ? "Preferred contact: $preferred_contact\n" : "") .
"\nMessage:\n$message\n\n" .
"IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n" .
"User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

// Deliverability-safe headers:
// Use your domain as From, put the visitor in Reply-To
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: ITME Website <support@itme573.com>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";

if (mail("support@itme573.com", $subject, $email_message, $headers)) {
  header("location: ../mail-success.html");
  exit;
}

echo "Email sending failed.";
