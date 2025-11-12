<?php
require_once __DIR__ . '/../config/db.php';

function get_user_profile($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, name, email, profile_picture_mime, created_at FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    
    return $stmt->fetch();
}

function get_user_profile_picture($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT profile_picture_blob, profile_picture_mime FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    
    return $stmt->fetch();
}

function update_profile_picture($user_id, $file_blob, $file_mime) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET profile_picture_blob = ?, 
            profile_picture_mime = ?,
            profile_picture_updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
        LIMIT 1
    ");
    
    return $stmt->execute([$file_blob, $file_mime, $user_id]);
}

function delete_profile_picture($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET profile_picture_blob = NULL, 
            profile_picture_mime = NULL,
            profile_picture_updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
        LIMIT 1
    ");
    
    return $stmt->execute([$user_id]);
}

function get_user_initials($name) {
    $words = explode(' ', trim($name));
    if (count($words) >= 2) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr($name, 0, 1));
}