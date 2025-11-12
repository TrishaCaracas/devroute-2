<?php
include "helpers/session.php";
include "models/registration.php";

$errors = [];

if(isset($_SESSION['id'])) {
    header("Location: profile");
    exit;
}

if(isset($_POST['submit'])) {
    if(!$_POST['name']) {
        $errors[] = "Name is required.";
    }
    
    if(!$_POST['email']) {
        $errors[] = "Email is required.";
    }
    
    if(!$_POST['password']) {
        $errors[] = "Password is required.";
    }
    
    if(empty($errors)) {
        if(!check_existing_email($_POST['email'])) {
            $user = save_registration($_POST['name'], $_POST['email'], $_POST['password']);
            
            if(!empty($user)) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['flash_message'] = "You have successfully created an account.";
                header("Location: blogs");
                exit;
            } else {
                $errors[] = "There was an error creating your account.";
            }
        } else {
            $errors[] = "Email address already exists.";
        }
    }
} else {
    $_POST = [
        'name' => '',
        'email' => '',
        'password' => ''
    ];
}
?>
<?php include 'layouts/_header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-box">
            <div class="auth-form-container">
                <h2>Create Your DevRoute Account</h2>
                <?php include 'layouts/_errors.php'; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
                
                <p class="auth-link">Already have an account? <a href="signin">Sign In</a></p>
            </div>
            
            <div class="auth-image-container">
                <img src="assets/images/devroute.png" alt="DevRoute Promotion">
            </div>
        </div>
    </div>
</section>

<?php include 'layouts/_footer.php'; ?>