<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul>
        <?php foreach ($errors as $row): ?>
            <li><?= htmlspecialchars($row) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>