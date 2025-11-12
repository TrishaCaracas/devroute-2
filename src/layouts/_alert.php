<?php if (isset($_SESSION['flash_message'])) { 
    $msg = $_SESSION['flash_message'];
    // Handle both string and array formats
    if (is_array($msg)) {
        $type = $msg['type'] ?? 'info';
        $text = $msg['text'] ?? '';
    } else {
        $type = 'info';
        $text = $msg;
    }
?>
    <div class="alert alert-<?= $type ?> alert-dismissible">
        <?= htmlspecialchars($text) ?>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php } ?>