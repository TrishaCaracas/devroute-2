<?php
include "helpers/session.php";
include "helpers/require_login.php";
include "models/certificate.php";

$errors = [];

function set_flash($type, $text) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'text' => $text
    ];
}

function normalize_date($date_input) {
    if (empty($date_input)) {
        return null;
    }

    $date = DateTime::createFromFormat('Y-m-d', $date_input);
    if (!$date) {
        $date = DateTime::createFromFormat('d/m/Y', $date_input);
    }

    return $date ? $date->format('Y-m-d') : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $issuer = trim($_POST['issuer'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $visibility = $_POST['visibility'] ?? 'private';
    $issued_date = normalize_date($_POST['issued_date'] ?? '');

    try {
        if ($action === 'create') {
            if ($title === '' || $issuer === '') {
                $errors[] = "Title and Issuer are required.";
            }

            if (!isset($_FILES['certificate_file']) || $_FILES['certificate_file']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Certificate file is required.";
            }

            if (empty($errors)) {
                $file = $_FILES['certificate_file'];

                $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type. Only PDF, JPG, and PNG are allowed.";
                }

                $max_size = 8 * 1024 * 1024; // 8MB
                if ($file['size'] > $max_size) {
                    $errors[] = "File is too large. Maximum size is 8MB.";
                }

                if (empty($errors)) {
                    $file_blob = file_get_contents($file['tmp_name']);

                    create_certificate($_SESSION['id'], [
                        'title' => $title,
                        'issuer' => $issuer,
                        'file_name' => $file['name'],
                        'file_mime' => $file['type'],
                        'file_blob' => $file_blob,
                        'tag' => $tag !== '' ? $tag : null,
                        'visibility' => $visibility === 'public' ? 'public' : 'private',
                        'issued_date' => $issued_date
                    ]);

                    set_flash('success', 'Certificate uploaded successfully.');
                    header("Location: vault");
                    exit;
                }
            }
        } elseif ($action === 'update') {
            $certificate_id = (int) ($_POST['certificate_id'] ?? 0);

            if ($certificate_id <= 0) {
                $errors[] = "Invalid certificate.";
            }

            if ($title === '' || $issuer === '') {
                $errors[] = "Title and Issuer are required.";
            }

            $payload = [
                'title' => $title,
                'issuer' => $issuer,
                'tag' => $tag !== '' ? $tag : null,
                'visibility' => $visibility === 'public' ? 'public' : 'private',
                'issued_date' => $issued_date
            ];

            if (isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['certificate_file'];
                $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type. Only PDF, JPG, and PNG are allowed.";
                } elseif ($file['size'] > (8 * 1024 * 1024)) {
                    $errors[] = "File is too large. Maximum size is 8MB.";
                } else {
                    $payload['file_name'] = $file['name'];
                    $payload['file_mime'] = $file['type'];
                    $payload['file_blob'] = file_get_contents($file['tmp_name']);
                }
            }

            if (empty($errors)) {
                if (!get_certificate_for_user($certificate_id, $_SESSION['id'])) {
                    $errors[] = "Certificate not found.";
                } else {
                    update_certificate($certificate_id, $_SESSION['id'], $payload);
                    set_flash('success', 'Certificate updated successfully.');
                    header("Location: vault");
                    exit;
                }
            }
        } elseif ($action === 'delete') {
            $certificate_id = (int) ($_POST['certificate_id'] ?? 0);
            if ($certificate_id <= 0) {
                $errors[] = "Invalid certificate.";
            } else {
                delete_certificate($certificate_id, $_SESSION['id']);
                set_flash('success', 'Certificate deleted successfully.');
                header("Location: vault");
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Vault action error: " . $e->getMessage());
        $errors[] = "An unexpected error occurred. Please try again.";
    }
}

$certificates = get_user_certificates($_SESSION['id']);
?>
<?php include 'layouts/_header.php'; ?>

<section class="vault-section">
    <div class="container">
        <div class="vault-header">
            <div class="vault-header-info">
                <h1>Your Certificate Vault</h1>
                <p class="subtitle">Manage your completed certificates and achievements.</p>
            </div>
            <button class="btn btn-secondary" id="open-vault-modal">Upload Certificate</button>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($certificates)): ?>
            <div class="empty-state">
                <p>You haven't uploaded any certificates yet.</p>
                <p>Click the <strong>Upload Certificate</strong> button to get started.</p>
            </div>
        <?php else: ?>
            <div class="vault-grid">
                <?php foreach ($certificates as $certificate): ?>
                    <div class="certificate-card" data-certificate='<?php echo htmlspecialchars(json_encode([
                        'id' => $certificate['id'],
                        'title' => $certificate['title'],
                        'issuer' => $certificate['issuer'],
                        'tag' => $certificate['tag'],
                        'visibility' => $certificate['visibility'],
                        'issued_date' => $certificate['issued_date'],
                    ]), ENT_QUOTES, 'UTF-8'); ?>'>
                        <div class="certificate-header">
                            <h3><?php echo htmlspecialchars($certificate['title']); ?></h3>
                            <span class="visibility <?php echo htmlspecialchars($certificate['visibility']); ?>">
                                <?php echo ucfirst($certificate['visibility']); ?>
                            </span>
                        </div>

                        <div class="certificate-preview">
                            <?php if (strpos($certificate['file_mime'], 'image/') === 0): ?>
                                <img src="certificate_download.php?id=<?php echo $certificate['id']; ?>&preview=1" alt="Certificate preview">
                            <?php elseif ($certificate['file_mime'] === 'application/pdf'): ?>
                                <iframe src="certificate_download.php?id=<?php echo $certificate['id']; ?>&preview=1" title="Certificate preview"></iframe>
                            <?php else: ?>
                                <div class="certificate-placeholder">
                                    <span class="file-extension">
                                        <?php echo htmlspecialchars(strtoupper(pathinfo($certificate['file_name'], PATHINFO_EXTENSION))); ?>
                                    </span>
                                    <small>Preview not available</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <p class="issuer">Issued by <?php echo htmlspecialchars($certificate['issuer']); ?></p>

                        <div class="certificate-meta">
                            <?php if (!empty($certificate['tag'])): ?>
                                <span class="certificate-tag"><?php echo htmlspecialchars($certificate['tag']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($certificate['issued_date'])): ?>
                                <span class="issued-date">Issued: <?php echo htmlspecialchars(date('M d, Y', strtotime($certificate['issued_date']))); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="certificate-actions">
                            <a class="btn btn-outline btn-sm" href="certificate_download.php?id=<?php echo $certificate['id']; ?>" target="_blank" rel="noopener noreferrer">View</a>
                            <button class="btn btn-outline btn-sm edit-certificate" data-id="<?php echo $certificate['id']; ?>">Edit</button>
                            <form method="POST" action="vault" class="inline-form" onsubmit="return confirm('Delete this certificate?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="certificate_id" value="<?php echo $certificate['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="vault-modal-overlay" id="vault-modal">
    <div class="vault-modal">
        <button class="modal-close" id="close-vault-modal">&times;</button>
        <h2 id="vault-modal-title">Upload Certificate</h2>
        <form id="vault-form" method="POST" action="vault" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="certificate_id" value="">

            <div class="form-group">
                <label for="certificate-title">Title *</label>
                <input type="text" id="certificate-title" name="title" placeholder="e.g., React Developer Certificate" required>
            </div>

            <div class="form-group">
                <label for="certificate-issuer">Issuer *</label>
                <input type="text" id="certificate-issuer" name="issuer" placeholder="e.g., FreeCodeCamp" required>
            </div>

            <div class="form-group">
                <label for="certificate-file">File * (PDF, JPG, PNG)</label>
                <div class="file-input-group">
                    <input type="text" id="certificate-file-name" class="file-name-display" placeholder="No file selected" readonly>
                    <label for="certificate-file" class="btn btn-secondary file-input-button">Choose file</label>
                    <input type="file" id="certificate-file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-group">
                <label for="certificate-tag">Tag</label>
                <input type="text" id="certificate-tag" name="tag" placeholder="e.g., JavaScript">
            </div>

            <div class="form-group">
                <label for="certificate-visibility">Visibility</label>
                <select id="certificate-visibility" name="visibility">
                    <option value="private">Private</option>
                    <option value="public">Public</option>
                </select>
            </div>

            <div class="form-group">
                <label for="certificate-issued-date">Issued Date</label>
                <input type="date" id="certificate-issued-date" name="issued_date">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="vault-submit-button">Upload</button>
            </div>
        </form>
    </div>
</div>

<?php include 'layouts/_footer.php'; ?>

