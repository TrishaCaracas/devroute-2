<?php
require_once __DIR__ . '/../config/db.php';

function check_existing_email($email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    return $stmt->fetch() ? true : false;
}

function save_registration($name, $email, $password) {
    global $pdo;
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    if($stmt->execute([$name, $email, $hashed_password])) {
        $user_id = $pdo->lastInsertId();
        return [
            'id' => $user_id,
            'name' => $name,
            'email' => $email
        ];
    }
    
    return null;
}
?>