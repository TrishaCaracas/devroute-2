<?php
include "helpers/session.php";
include "helpers/require_login.php";
?>
<?php include 'layouts/_header.php'; ?>

<section class="roadmaps-page">
    <div class="container">
        <h1>Explore Career Roadmaps</h1>
        <p class="subtitle">Choose from various tech career paths</p>
        
        <div class="roadmaps-grid">
            <div class="roadmap-card">
                <img src="assets/images/web-dev.png" alt="Web Development">
                <h3>Web Development</h3>
                <p class="long-description">
                    Build modern, responsive websites and web apps that scale. This path covers core frontend skills
                    (HTML, CSS, JavaScript, frameworks like React or Vue), backend development (APIs, databases,
                    authentication), and deployment practices. Great for problem-solvers who enjoy rapid iteration and
                    working across the stack to deliver delightful user experiences.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱900,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="High" aria-label="Job Demand">High</span>
                    </div>
                </div>
                <a href="profile?career_path=Web Development" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
            
            <div class="roadmap-card">
                <img src="assets/images/data-science.png" alt="Data Science">
                <h3>Data Science</h3>
                <p class="long-description">
                    Turn data into decisions with statistics, Python, and machine learning. Learn to wrangle datasets,
                    build predictive models, visualize insights, and communicate impact. Ideal for analytical thinkers
                    who like experimentation, A/B testing, and model lifecycle management from prototyping to production.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱1,200,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="Very High" aria-label="Job Demand">Very High</span>
                    </div>
                </div>
                <a href="profile?career_path=Data Science" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
            
            <div class="roadmap-card">
                <img src="assets/images/cybersecurity.png" alt="Cybersecurity">
                <h3>Cybersecurity</h3>
                <p class="long-description">
                    Defend systems and data against evolving threats. Explore network security, vulnerability assessment,
                    threat modeling, incident response, and compliance. Perfect for detail‑oriented learners who enjoy
                    thinking like an attacker to strengthen defenses and keep organizations resilient.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱1,000,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="Growing" aria-label="Job Demand">Growing</span>
                    </div>
                </div>
                <a href="profile?career_path=Cybersecurity" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
            
            <div class="roadmap-card">
                <img src="assets/images/cloud.png" alt="Cloud Computing">
                <h3>Cloud Computing</h3>
                <p class="long-description">
                    Design, deploy, and operate scalable systems on AWS, Azure, or GCP. Learn cloud architecture,
                    containerization, serverless, observability, and cost optimization. Suited for builders who like
                    reliability engineering and translating business needs into robust cloud solutions.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱1,300,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="Very High" aria-label="Job Demand">Very High</span>
                    </div>
                </div>
                <a href="profile?career_path=Cloud Computing" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
            
            <div class="roadmap-card">
                <img src="assets/images/mobile.png" alt="Mobile Development">
                <h3>Mobile Development</h3>
                <p class="long-description">
                    Craft performant iOS and Android apps with native stacks or cross‑platform frameworks like Flutter
                    and React Native. Focus on mobile UI/UX, offline‑first patterns, device APIs, and app store delivery.
                    Great for those who love pixel‑perfect interactions and shipping to millions of users.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱950,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="High" aria-label="Job Demand">High</span>
                    </div>
                </div>
                <a href="profile?career_path=Mobile Development" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
            
            <div class="roadmap-card">
                <img src="assets/images/devops.png" alt="DevOps">
                <h3>DevOps Engineering</h3>
                <p class="long-description">
                    Bridge development and operations with CI/CD, automation, and infrastructure as code. Learn pipelines,
                    containers, Kubernetes, monitoring, and incident management. Ideal for engineers who enjoy improving
                    delivery speed, reliability, and developer experience across teams.
                </p>
                <div class="roadmap-meta">
                    <div class="meta-row">
                        <span class="meta-label">Average Salary</span>
                        <span class="meta-value salary-badge" aria-label="Average Salary">₱1,250,000 avg/yr</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Job Demand</span>
                        <span class="meta-value demand-badge" data-demand="High" aria-label="Job Demand">High</span>
                    </div>
                </div>
                <a href="profile?career_path=DevOps Engineering" class="btn btn-primary btn-sm roadmap-cta">View Roadmap</a>
            </div>
        </div>
    </div>
</section>

<?php include 'layouts/_footer.php'; ?>