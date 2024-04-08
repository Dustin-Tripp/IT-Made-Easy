<?php
// Check if reCAPTCHA response is set
if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret = '6LfBr7EpAAAAANnyeb0ZuM1sZhPdwi8KPY_uTIp3'; // Your reCAPTCHA secret key

    // Verify the reCAPTCHA response
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha_response}");
    $responseData = json_decode($verifyResponse);

    if ($responseData->success) {
        // reCAPTCHA verified successfully

        // Process your form data here
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $service = isset($_POST['service']) ? $_POST['service'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $date = isset($_POST['date']) ? $_POST['date'] : ''; // Capturing the date

        $subject = "New Inquiry from $name"; // Customize the subject

        $email_message = "
        Name: $name
        Email: $email
        Phone: $phone
        Service Interested In: $service
        Preferred Consultation Date: $date
        Message: $message
        ";

        // Set content-type header for sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: <$email>" . "\r\n";

        // Send the email
        if (mail("support@itme573.com", $subject, $email_message, $headers)) {
            header("location: ../mail-success.html"); // Redirect on success
        } else {
            echo "Email sending failed."; // Error message
        }
    } else {
        // reCAPTCHA verification failed
        echo "CAPTCHA verification failed. Please try again.";
    }
} else {
    echo "No CAPTCHA response received.";
}
?>
