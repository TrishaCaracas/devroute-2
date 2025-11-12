<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevRoute</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index" class="logo">DevRoute</a>
            <ul class="nav-links">
                <?php if(isset($_SESSION['id'])): ?>
                    <li><a href="profile">Profile</a></li>
                    <li><a href="roadmaps">Roadmaps</a></li>
                    <li><a href="vault">Vault</a></li>
                    <li><a href="logout?logout=true">Logout</a></li>
                <?php else: ?>
                    <li><a href="index">Home</a></li>
                    <li><a href="signin">Sign In</a></li>
                    <li><a href="signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <?php include '_alert.php'; ?>