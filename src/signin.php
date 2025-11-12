<?php
include "helpers/session.php";
include "models/login.php";

$errors = [];

if(isset($_SESSION['id'])) {
    header("Location: profile");
    exit;
}

if(isset($_POST['submit'])) {
    if(!$_POST['email']) {
        $errors[] = "Email is required.";
    }
    
    if(!$_POST['password']) {
        $errors[] = "Password is required.";
    }
    
    if(empty($errors)) {
        $user = login_account($_POST['email'], $_POST['password']);
        
        if(!empty($user)) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => "You have successfully logged in."
            ];
            header("Location: profile");
            exit;
        } else {
            $errors[] = "The email or password you've entered is incorrect.";
        }
    }
} else {
    $_POST = [
        'email' => '',
        'password' => ''
    ];
}
?>
<?php include 'layouts/_header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-box">
            <h2>Sign In to DevRoute</h2>
            <?php include 'layouts/_errors.php'; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
            
            <p class="auth-link">Don't have an account? <a href="signup">Sign Up</a></p>
        </div>
    </div>
</section>

<?php include 'layouts/_footer.php'; ?>