<?php
include "helpers/session.php";
include "helpers/require_login.php";
include "models/certificate.php";

$certificate_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($certificate_id <= 0) {
    http_response_code(400);
    echo "Invalid certificate.";
    exit;
}

$certificate = get_certificate_for_user($certificate_id, $_SESSION['id']);

if (!$certificate) {
    http_response_code(404);
    echo "Certificate not found.";
    exit;
}

$mime = $certificate['file_mime'] ?: 'application/octet-stream';
$filename = $certificate['file_name'] ?: ('certificate_' . $certificate['id']);

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . strlen($certificate['file_blob']));
header('Cache-Control: private, max-age=0, must-revalidate');

echo $certificate['file_blob'];
exit;

