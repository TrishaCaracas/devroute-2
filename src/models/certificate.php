<?php
require_once __DIR__ . '/../config/db.php';

function get_user_certificates($user_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT id, title, issuer, file_name, file_mime, tag, visibility, issued_date, created_at, updated_at
        FROM certificates
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);

    return $stmt->fetchAll();
}

function get_certificate_for_user($certificate_id, $user_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM certificates
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$certificate_id, $user_id]);

    return $stmt->fetch();
}

function create_certificate($user_id, $data) {
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO certificates (
            user_id, title, issuer, file_name, file_mime, file_blob, tag, visibility, issued_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $user_id,
        $data['title'],
        $data['issuer'],
        $data['file_name'],
        $data['file_mime'],
        $data['file_blob'],
        $data['tag'] ?? null,
        $data['visibility'] ?? 'private',
        $data['issued_date'] ?? null
    ]);
}

function update_certificate($certificate_id, $user_id, $data) {
    global $pdo;

    $fields = [
        'title' => $data['title'],
        'issuer' => $data['issuer'],
        'tag' => $data['tag'] ?? null,
        'visibility' => $data['visibility'] ?? 'private',
        'issued_date' => $data['issued_date'] ?? null,
    ];

    $set_clauses = [
        "title = :title",
        "issuer = :issuer",
        "tag = :tag",
        "visibility = :visibility",
        "issued_date = :issued_date",
    ];

    if (isset($data['file_blob'])) {
        $fields['file_name'] = $data['file_name'];
        $fields['file_mime'] = $data['file_mime'];
        $fields['file_blob'] = $data['file_blob'];
        $set_clauses[] = "file_name = :file_name";
        $set_clauses[] = "file_mime = :file_mime";
        $set_clauses[] = "file_blob = :file_blob";
    }

    $sql = "
        UPDATE certificates
        SET " . implode(", ", $set_clauses) . ",
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id AND user_id = :user_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $fields['id'] = $certificate_id;
    $fields['user_id'] = $user_id;

    return $stmt->execute($fields);
}

function delete_certificate($certificate_id, $user_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        DELETE FROM certificates
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");

    return $stmt->execute([$certificate_id, $user_id]);
}

