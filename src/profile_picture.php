<?php
include "helpers/session.php";
include "helpers/require_login.php";
include "models/user.php";

$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : $_SESSION['id'];

$profile_picture = get_user_profile_picture($user_id);

if (!$profile_picture || !$profile_picture['profile_picture_blob']) {
    http_response_code(404);
    header('Content-Type: text/plain');
    echo "No profile picture found.";
    exit;
}

$mime = $profile_picture['profile_picture_mime'] ?: 'image/jpeg';

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="profile_' . $user_id . '.jpg"');
header('Content-Length: ' . strlen($profile_picture['profile_picture_blob']));
header('Cache-Control: private, max-age=3600, must-revalidate');

echo $profile_picture['profile_picture_blob'];
exit;