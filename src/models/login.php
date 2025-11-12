<?php
require_once __DIR__ . '/../config/db.php';

function login_account($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        return $user;
    }
    
    return null;
}
?>