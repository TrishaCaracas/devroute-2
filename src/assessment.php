<?php
include "helpers/session.php";
include "models/assessment.php";

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$total_steps = 5;

if(isset($_POST['submit_assessment'])) {
    // Process assessment results
    $career_path = determine_career_path($_POST);
    
    if(isset($_SESSION['id'])) {
        save_assessment_result(
            $_SESSION['id'],
            $career_path,
            $_POST['personality'] ?? '',
            $_POST['skills'] ?? '',
            $_POST['work_style'] ?? '',
            $_POST
        );
        
        $_SESSION['flash_message'] = "Assessment completed! Your recommended path: " . $career_path;
        header("Location: profile");
        exit;
    } else {
        $_SESSION['assessment_result'] = $career_path;
        header("Location: signup");
        exit;
    }
}

function determine_career_path($responses) {
    // Simple logic to determine career path
    $paths = ['Web Development', 'Data Science', 'Cybersecurity', 'Cloud Computing', 'Mobile Development'];
    return $paths[array_rand($paths)];
}
?>
<?php include 'layouts/_header.php'; ?>

<section class="assessment-section">
    <div class="container">
        <div class="assessment-container">
            <h1>Tech Career Assessment</h1>
            <div class="progress-bar">
                <div class="progress" style="width: <?php echo ($step / $total_steps) * 100; ?>%"></div>
            </div>
            <p class="step-indicator">Step <?php echo $step; ?> of <?php echo $total_steps; ?></p>
            
            <form method="POST" action="?step=<?php echo $step + 1; ?>">
                <?php if($step == 1): ?>
                    <h2>What describes you best?</h2>
                    <div class="options-grid">
                        <label class="option-card">
                            <input type="radio" name="personality" value="creative" required>
                            <span>Creative Problem Solver</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="personality" value="analytical">
                            <span>Analytical Thinker</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="personality" value="detail">
                            <span>Detail-Oriented</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="personality" value="big_picture">
                            <span>Big Picture Thinker</span>
                        </label>
                    </div>
                
                <?php elseif($step == 2): ?>
                    <h2>What's your current skill level?</h2>
                    <div class="options-grid">
                        <label class="option-card">
                            <input type="radio" name="skills" value="beginner" required>
                            <span>Beginner</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="skills" value="intermediate">
                            <span>Intermediate</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="skills" value="advanced">
                            <span>Advanced</span>
                        </label>
                    </div>
                
                <?php elseif($step == 3): ?>
                    <h2>What work environment do you prefer?</h2>
                    <div class="options-grid">
                        <label class="option-card">
                            <input type="radio" name="work_style" value="collaborative" required>
                            <span>Collaborative Team</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="work_style" value="independent">
                            <span>Independent Work</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="work_style" value="flexible">
                            <span>Flexible Mix</span>
                        </label>
                    </div>
                
                <?php elseif($step == 4): ?>
                    <h2>What interests you most?</h2>
                    <div class="options-grid">
                        <label class="option-card">
                            <input type="radio" name="interest" value="visual" required>
                            <span>Visual Design & UX</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="interest" value="data">
                            <span>Data & Analytics</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="interest" value="security">
                            <span>Security & Protection</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="interest" value="infrastructure">
                            <span>Systems & Infrastructure</span>
                        </label>
                    </div>
                
                <?php elseif($step == 5): ?>
                    <h2>What's your learning style?</h2>
                    <div class="options-grid">
                        <label class="option-card">
                            <input type="radio" name="learning" value="hands_on" required>
                            <span>Hands-On Practice</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="learning" value="theory">
                            <span>Theory First</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="learning" value="projects">
                            <span>Project-Based</span>
                        </label>
                    </div>
                <?php endif; ?>
                
                <div class="button-group">
                    <?php if($step > 1): ?>
                        <a href="?step=<?php echo $step - 1; ?>" class="btn btn-secondary">Back</a>
                    <?php endif; ?>
                    
                    <?php if($step < $total_steps): ?>
                        <button type="submit" class="btn btn-primary">Next</button>
                    <?php else: ?>
                        <button type="submit" name="submit_assessment" class="btn btn-primary">Complete Assessment</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include 'layouts/_footer.php'; ?>