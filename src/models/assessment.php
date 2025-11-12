<?php
require_once __DIR__ . '/../config/db.php';

function save_assessment_result($user_id, $career_path, $personality, $skills, $work_style, $data) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO assessment_results (user_id, career_path, personality_type, skills_level, work_style, assessment_data) VALUES (?, ?, ?, ?, ?, ?)");
    
    return $stmt->execute([$user_id, $career_path, $personality, $skills, $work_style, json_encode($data)]);
}

function get_user_assessment($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM assessment_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    
    return $stmt->fetch();
}
?>