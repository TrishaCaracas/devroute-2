<?php
require_once __DIR__ . '/../config/db.php';

function create_roadmap($user_id, $career_path, $title, $description) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO roadmaps (user_id, career_path, title, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $career_path, $title, $description]);
    
    return $pdo->lastInsertId();
}

function get_user_roadmaps($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM roadmaps WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    
    return $stmt->fetchAll();
}

function get_roadmap_milestones($roadmap_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM milestones WHERE roadmap_id = ? ORDER BY order_num ASC");
    $stmt->execute([$roadmap_id]);
    
    return $stmt->fetchAll();
}

function get_milestone_resources($milestone_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM resources WHERE milestone_id = ?");
    $stmt->execute([$milestone_id]);
    
    return $stmt->fetchAll();
}
?>