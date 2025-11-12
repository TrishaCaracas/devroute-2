<?php
include "helpers/session.php";

if(isset($_SESSION['id'])) {
    header("Location: blogs");
    exit;
}
?>
<?php include 'layouts/_header.php'; ?>

<section class="hero">
    <div class="container">
        <h1>Navigate Your Tech Career Journey</h1>
        <p>Discover your perfect tech career path with personalized assessments and guided roadmaps</p>
        <a href="assessment" class="btn btn-primary">Start Your Journey</a>
    </div>
</section>

<section class="domains">
    <div class="container">
        <h2>Explore IT Domains</h2>
        <div class="domains-grid">
            <div class="domain-card">
                <h3>Web Development</h3>
                <p>Build interactive websites and applications</p>
            </div>
            <div class="domain-card">
                <h3>Data Science</h3>
                <p>Analyze data and build predictive models</p>
            </div>
            <div class="domain-card">
                <h3>Cybersecurity</h3>
                <p>Protect systems and data from threats</p>
            </div>
            <div class="domain-card">
                <h3>Cloud Computing</h3>
                <p>Design and manage cloud infrastructure</p>
            </div>
            <div class="domain-card">
                <h3>Mobile Development</h3>
                <p>Create apps for iOS and Android</p>
            </div>
            <div class="domain-card">
                <h3>DevOps</h3>
                <p>Streamline development and operations</p>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2>Core Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>Career Assessment</h3>
                <p>Take our comprehensive quiz to discover your ideal tech career path</p>
            </div>
            <div class="feature-card">
                <h3>Personalized Roadmaps</h3>
                <p>Get step-by-step guidance tailored to your career goals</p>
            </div>
            <div class="feature-card">
                <h3>Curated Resources</h3>
                <p>Access quality learning materials from top platforms</p>
            </div>
        </div>
    </div>
</section>

<?php include 'layouts/_footer.php'; ?>