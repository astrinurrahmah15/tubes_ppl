<?php
// Fungsi sendEmail dinonaktifkan sesuai permintaan user
function sendEmail(
    $to, $subject, $message, $from_email, $from_name
) {
    // Email sending is disabled.
    return false;
}
?> 