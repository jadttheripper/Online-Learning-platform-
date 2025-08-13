<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send the password reset email
function sendPasswordResetEmail($email, $resetLink) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = '12031634@students.liu.edu.lb';  // Your Gmail address
        $mail->Password = 'ebnuqvwjiwgjrbrq'; // Your Gmail app password (16 character one)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  // TLS Port for Gmail

        // Recipients
        $mail->setFrom('12031634@students.liu.edu.lb', 'SkillSwap');  // Set your email as the sender
        $mail->addAddress($email);  // Add the recipient's email address

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - SkillSwap';
        $mail->Body    = '
            <p>Hello,</p>
            <p>We received a request to reset your password for your SkillSwap account.</p>
            <p>If you requested this, click the link below to reset your password:</p>
            <p><a href="' . $resetLink . '">Reset Your Password</a></p>
            <p>If you did not request a password reset, please ignore this message.</p>
            <p>Best regards,<br>SkillSwap Team</p>
        ';

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "❌ Mail could not be sent. PHPMailer Error: {$mail->ErrorInfo}";
    }
}
?>
