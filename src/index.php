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
        <img src="assets/images/devroute-logo.png" alt="devroute logo" class="hero-logo">
        <div class="tagline">Your personalized path to tech mastery</div>
        <h1><span>TECH</span> is VAST</h1>
        <h2>Navigate your path from <span class="accent">beginner</span> to <span class="accent2">expert</span></h2>
        <p>Discover your tech career path with personalized assessments and roadmaps.</p>
        <div class="hero-buttons">
            <a href="assessment" class="btn btn-primary">Start Your Journey</a>
            <a href="#domains" class="btn btn-secondary">Explore Paths</a>
        </div>
    </div>
</section>

<section class="domains">
  <div class="container">
    <h2>Choose Your <span>Domain</span></h2>
    <p class="domains-subtitle">
      From code to cloud, design to data — find the path that ignites your passion.
    </p>
    <div class="domains-grid">

      <div class="domain-card">
        <img src="assets/images/web-development-icon.png" alt="Development Icon" class="domain-icon">
        <h3>Web Development</h3>
        <p>Build interactive websites and applications</p>
      </div>

      <div class="domain-card">
        <img src="assets/images/data-science-icon.png" alt="Data Science Icon" class="domain-icon">
        <h3>Data Science</h3>
        <p>Analyze data and build predictive models</p>
      </div>

      <div class="domain-card">
        <img src="assets/iimages/cybersecurity-icon.png" alt="Cybersecurity Icon" class="domain-icon">
        <h3>Cybersecurity</h3>
        <p>Protect systems and data from threats</p>
      </div>

      <div class="domain-card">
        <img src="assets/images/cloud-icon.png" alt="Cloud Icon" class="domain-icon">
        <h3>Cloud Computing</h3>
        <p>Design and manage cloud infrastructure</p>
      </div>

      <div class="domain-card">
        <img src="assets/images/mobile-icon.png" alt="Mobile Icon" class="domain-icon">
        <h3>Mobile Development</h3>
        <p>Create apps for iOS and Android</p>
      </div>

      <div class="domain-card">
        <img src="assets/images/devops-icon.png" alt="DevOps Icon" class="domain-icon">
        <h3>DevOps</h3>
        <p>Streamline development and operations</p>
      </div>

    </div>
  </div>
</section>

<section id="features" class="features">
  <div class="container">
    <h2>Everything you need to <span>Suceed</span></h2>
    <p class="feature-subtitle"> A complete ecosystem designed to accelerate your journey to expertise </p>
    <div class="features-grid">
      <div class="feature-card">
        <img src="career-icon.png" alt="Feature Icon" class="feature-icon">
        <h3>Career Assessment</h3>
        <p>Take our comprehensive quiz to discover your ideal tech career path</p>
      </div>
      <div class="feature-card">
        <img src="roadmap-icon.png" alt="Feature Icon" class="feature-icon">
        <h3>Personalized Roadmaps</h3>
        <p>Get step-by-step guidance tailored to your career goals</p>
      </div>
      <div class="feature-card">
        <img src="resources-icon.png" alt="Feature Icon" class="feature-icon">
        <h3>Curated Resources</h3>
        <p>Access quality learning materials from top platforms</p>
      </div>
      <div class="feature-card">
        <img src="vault-icon.png" alt="Feature Icon" class="feature-icon">
        <h3>Credential Vault</h3>
        <p>Store your certificates and accomplishments in a centralized vault</p>
      </div>
    </div>
  </div>
</section>

<?php include 'layouts/_footer.php'; ?>