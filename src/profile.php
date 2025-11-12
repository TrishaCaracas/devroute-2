<?php
include "helpers/session.php";
include "helpers/require_login.php";
include "models/assessment.php";
include "models/roadmap.php";
include "models/user.php";

$errors = [];

// Handle profile picture upload/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'upload_picture') {
            if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Please select a valid image file.";
            } else {
                $file = $_FILES['profile_picture'];
                
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!in_array($file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
                }
                
                // Validate file size (max 5MB)
                $max_size = 5 * 1024 * 1024;
                if ($file['size'] > $max_size) {
                    $errors[] = "File is too large. Maximum size is 5MB.";
                }
                
                if (empty($errors)) {
                    $file_blob = file_get_contents($file['tmp_name']);
                    
                    if (update_profile_picture($_SESSION['id'], $file_blob, $file['type'])) {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'text' => 'Profile picture updated successfully.'
                        ];
                    } else {
                        $errors[] = "Failed to update profile picture.";
                    }
                    
                    if (empty($errors)) {
                        header("Location: profile");
                        exit;
                    }
                }
            }
        } elseif ($action === 'delete_picture') {
            if (delete_profile_picture($_SESSION['id'])) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Profile picture deleted successfully.'
                ];
                header("Location: profile");
                exit;
            } else {
                $errors[] = "Failed to delete profile picture.";
            }
        }
    } catch (Exception $e) {
        error_log("Profile picture error: " . $e->getMessage());
        $errors[] = "An unexpected error occurred. Please try again.";
    }
}

// Initialize variables
$assessment = false;
$roadmaps = [];
$selected_career_path = null;
$user_profile = null;

// Check if a specific career path was requested via URL parameter
if(isset($_GET['career_path']) && !empty($_GET['career_path'])) {
    $selected_career_path = htmlspecialchars(trim($_GET['career_path']));
}

// Wrap database calls in try-catch to handle errors gracefully
try {
    $user_profile = get_user_profile($_SESSION['id']);
    $assessment = get_user_assessment($_SESSION['id']);
    $roadmaps = get_user_roadmaps($_SESSION['id']);
    
    // If a career path was selected from roadmaps page, create/display that roadmap
    if($selected_career_path) {
        // Check if user already has a roadmap for this career path
        $existing_roadmap = null;
        foreach($roadmaps as $roadmap) {
            if(isset($roadmap['career_path']) && $roadmap['career_path'] === $selected_career_path) {
                $existing_roadmap = $roadmap;
                break;
            }
        }
        
        // If no roadmap exists for this career path, create one
        if(!$existing_roadmap) {
            $roadmap_id = create_roadmap(
                $_SESSION['id'],
                $selected_career_path,
                $selected_career_path . " Learning Path",
                "Your personalized roadmap to become a " . $selected_career_path ." Professional"
            );
            $roadmaps = get_user_roadmaps($_SESSION['id']); // Refresh roadmaps
        }
    }
    // Create default roadmap if none exists and no specific path was selected
    elseif(empty($roadmaps) && $assessment) {
        $roadmap_id = create_roadmap(
            $_SESSION['id'],
            $assessment['career_path'],
            $assessment['career_path'] . " Learning Path",
            "Your personalized roadmap to become a " . $assessment['career_path'] . " Professional"
        );
        $roadmaps = get_user_roadmaps($_SESSION['id']);
    }
} catch (Exception $e) {
    // Log error but don't break the page
    error_log("Profile page error: " . $e->getMessage());
    // $assessment and $roadmaps remain as initialized above
}
?>
<?php include 'layouts/_header.php'; ?>

<section class="profile-section">
    <div class="container">
        <div class="profile-header">
            <div class="profile-picture-section">
                <div class="profile-picture-container">
                    <?php if ($user_profile && $user_profile['profile_picture_mime']): ?>
                        <img src="profile_picture.php?user_id=<?php echo $_SESSION['id']; ?>&t=<?php echo time(); ?>" 
                             alt="Profile Picture" 
                             class="profile-picture"
                             id="profile-picture-img">
                    <?php else: ?>
                        <div class="profile-picture-placeholder" id="profile-picture-placeholder">
                            <span class="profile-initials">
                                <?php echo htmlspecialchars(get_user_initials($_SESSION['name'] ?? 'User')); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <button class="profile-picture-edit-btn" id="edit-profile-picture-btn" title="Change profile picture">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="profile-info">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!</h1>
                    <?php if($selected_career_path): ?>
                        <p class="career-path">Viewing Roadmap: <strong><?php echo htmlspecialchars($selected_career_path); ?></strong></p>
                        <p><a href="profile" class="btn btn-secondary">View My Assessment Roadmap</a></p>
                    <?php elseif($assessment && isset($assessment['career_path'])): ?>
                        <p class="career-path">Your Career Path: <strong><?php echo htmlspecialchars($assessment['career_path']); ?></strong></p>
                    <?php else: ?>
                        <p><a href="assessment" class="btn btn-primary">Take Career Assessment</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($roadmaps)): ?>
            <?php 
            // Filter roadmaps based on context:
            // 1. If career_path parameter exists, show only that roadmap (for browsing)
            // 2. Otherwise, show only the roadmap matching the assessment result
            if($selected_career_path) {
                // Show the selected roadmap from roadmaps page
                $filtered_roadmaps = array_filter($roadmaps, function($roadmap) use ($selected_career_path) {
                    return isset($roadmap['career_path']) && $roadmap['career_path'] === $selected_career_path;
                });
                if(!empty($filtered_roadmaps)) {
                    $roadmaps = $filtered_roadmaps;
                }
            } elseif($assessment && isset($assessment['career_path'])) {
                // Show only the roadmap matching the assessment result
                $filtered_roadmaps = array_filter($roadmaps, function($roadmap) use ($assessment) {
                    return isset($roadmap['career_path']) && 
                           isset($assessment['career_path']) && 
                           $roadmap['career_path'] === $assessment['career_path'];
                });
                if(!empty($filtered_roadmaps)) {
                    $roadmaps = $filtered_roadmaps;
                } else {
                    // If no roadmap exists for assessment, show empty state
                    $roadmaps = [];
                }
            }
            ?>
            <?php foreach($roadmaps as $roadmap): ?>
                <div class="roadmap-container" data-roadmap-id="<?php echo $roadmap['id']; ?>">
                    <h2><?php echo htmlspecialchars($roadmap['title']); ?></h2>
                    <p><?php echo htmlspecialchars($roadmap['description']); ?></p>

                    <div class="roadmap-progress" data-roadmap-progress>
                        <div class="progress-header">
                            <span class="progress-title">Roadmap Progress</span>
                            <span class="progress-percentage">0%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-summary">
                            <span class="progress-count">0 of 0 completed</span>
                        </div>
                    </div>
                    
                    <div class="roadmap-visual">
                        <?php
                        $milestones = [];
                        try {
                            $milestones = get_roadmap_milestones($roadmap['id']);
                        } catch (Exception $e) {
                            error_log("Error fetching milestones: " . $e->getMessage());
                        }
                        
                        if(empty($milestones) && isset($roadmap['career_path'])) {
                            // Create default milestones for demo
                            $milestones = get_default_milestones($roadmap['career_path']);
                        }
                        
                        foreach($milestones as $index => $milestone):
                            // Handle both array and object formats
                            $milestone_title = is_array($milestone) ? ($milestone['title'] ?? '') : ($milestone->title ?? '');
                            $milestone_desc = is_array($milestone) ? ($milestone['description'] ?? '') : ($milestone->description ?? '');
                            $milestone_completed = is_array($milestone) ? ($milestone['completed'] ?? false) : ($milestone->completed ?? false);
                        ?>
                            <div class="milestone <?php echo $milestone_completed ? 'completed' : ''; ?>">
                                <div class="milestone-number"><?php echo $index + 1; ?></div>
                                <div class="milestone-content">
                                    <h3><?php echo htmlspecialchars($milestone_title); ?></h3>
                                    <p><?php echo htmlspecialchars($milestone_desc); ?></p>
                                    
                                    <div class="resources-dropdown">
                                        <button class="resources-toggle" onclick="toggleResources(<?php echo $index; ?>)">
                                            View Resources ▼
                                        </button>
                                        <div class="resources-list" id="resources-<?php echo $index; ?>">
                                            <?php
                                            $resources = get_default_resources($milestone_title);
                                            foreach($resources as $resource_index => $resource):
                                                $resource_id = 'resource-' . $roadmap['id'] . '-' . $index . '-' . $resource_index;
                                                $resource_key = hash('crc32b', ($roadmap['id'] ?? '') . '|' . $milestone_title . '|' . ($resource['url'] ?? '') . '|' . $resource_index);
                                            ?>
                                                <div class="resource-item">
                                                    <label class="resource-checkbox-label" for="<?php echo htmlspecialchars($resource_id); ?>">
                                                        <input 
                                                            type="checkbox" 
                                                            id="<?php echo htmlspecialchars($resource_id); ?>" 
                                                            class="resource-checkbox" 
                                                            data-roadmap-id="<?php echo htmlspecialchars($roadmap['id']); ?>" 
                                                            data-resource-key="<?php echo htmlspecialchars($resource_key); ?>"
                                                        >
                                                        <span class="resource-checkmark" aria-hidden="true"></span>
                                                    </label>
                                                    <a href="<?php echo htmlspecialchars($resource['url']); ?>" target="_blank" class="resource-link" rel="noopener noreferrer">
                                                        <span class="resource-platform"><?php echo htmlspecialchars($resource['platform']); ?></span>
                                                        <span class="resource-title"><?php echo htmlspecialchars($resource['title']); ?></span>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($index < count($milestones) - 1): ?>
                                    <div class="milestone-connector"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-roadmaps">
                <p id="new-user-message">You don't have any roadmaps yet. <a href="assessment">Take the assessment</a> or <a href="roadmaps">browse roadmaps</a> to get started!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Profile Picture Modal -->
<div class="profile-picture-modal-overlay" id="profile-picture-modal">
    <div class="profile-picture-modal">
        <button class="modal-close" id="close-profile-picture-modal">&times;</button>
        <h2>Update Profile Picture</h2>
        
        <div class="profile-picture-preview">
            <?php if ($user_profile && $user_profile['profile_picture_mime']): ?>
                <img src="profile_picture.php?user_id=<?php echo $_SESSION['id']; ?>&t=<?php echo time(); ?>" 
                     alt="Current Profile Picture" 
                     class="current-profile-picture"
                     id="modal-profile-picture">
            <?php else: ?>
                <div class="profile-picture-placeholder large" id="modal-profile-placeholder">
                    <span class="profile-initials">
                        <?php echo htmlspecialchars(get_user_initials($_SESSION['name'] ?? 'User')); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <form method="POST" action="profile" enctype="multipart/form-data" id="profile-picture-form">
            <input type="hidden" name="action" value="upload_picture">
            
            <div class="form-group">
                <label for="profile-picture-input" class="btn btn-primary btn-block">Choose New Picture</label>
                <input type="file" 
                       id="profile-picture-input" 
                       name="profile_picture" 
                       accept="image/jpeg,image/jpg,image/png,image/gif" 
                       style="display: none;">
                <p class="file-hint">JPG, PNG, or GIF (max 5MB)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-block" id="upload-picture-btn" disabled>Upload Picture</button>
            </div>
        </form>
        
        <?php if ($user_profile && $user_profile['profile_picture_mime']): ?>
            <form method="POST" action="profile" class="delete-picture-form" onsubmit="return confirm('Are you sure you want to delete your profile picture?');">
                <input type="hidden" name="action" value="delete_picture">
                <button type="submit" class="btn btn-danger btn-block">Delete Current Picture</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'layouts/_footer.php'; ?>

<?php
function get_default_milestones($career_path) {
    $milestones_map = [
        'Web Development' => [
            ['title' => 'HTML Fundamentals', 'description' => 'Learn the structure of web pages', 'completed' => false],
            ['title' => 'CSS Mastery', 'description' => 'Style and layout your websites', 'completed' => false],
            ['title' => 'JavaScript Basics', 'description' => 'Add interactivity to your pages', 'completed' => false],
            ['title' => 'Frontend Frameworks', 'description' => 'Master React or Vue.js', 'completed' => false],
            ['title' => 'Backend Development', 'description' => 'Learn Node.js or PHP', 'completed' => false],
            ['title' => 'Database Management', 'description' => 'Work with MySQL/PostgreSQL', 'completed' => false],
        ],
        'Data Science' => [
            ['title' => 'Python Fundamentals', 'description' => 'Learn Python programming', 'completed' => false],
            ['title' => 'Statistics & Math', 'description' => 'Master statistical concepts', 'completed' => false],
            ['title' => 'Data Analysis', 'description' => 'Work with Pandas and NumPy', 'completed' => false],
            ['title' => 'Machine Learning', 'description' => 'Build predictive models', 'completed' => false],
            ['title' => 'Deep Learning', 'description' => 'Neural networks and AI', 'completed' => false],
        ],
        'Cybersecurity' => [
            ['title' => 'Network Fundamentals', 'description' => 'Understanding network protocols', 'completed' => false],
            ['title' => 'Security Principles', 'description' => 'Core security concepts', 'completed' => false],
            ['title' => 'Ethical Hacking', 'description' => 'Penetration testing basics', 'completed' => false],
            ['title' => 'Security Tools', 'description' => 'Master security software', 'completed' => false],
        ],
        'Cloud Computing' => [
            ['title' => 'Cloud Fundamentals', 'description' => 'Understanding cloud services', 'completed' => false],
            ['title' => 'AWS/Azure Basics', 'description' => 'Major cloud platforms', 'completed' => false],
            ['title' => 'Cloud Architecture', 'description' => 'Design cloud solutions', 'completed' => false],
            ['title' => 'DevOps Integration', 'description' => 'CI/CD in the cloud', 'completed' => false],
        ],
        'Mobile Development' => [
            ['title' => 'Mobile Fundamentals', 'description' => 'iOS and Android basics', 'completed' => false],
            ['title' => 'React Native/Flutter', 'description' => 'Cross-platform development', 'completed' => false],
            ['title' => 'UI/UX for Mobile', 'description' => 'Mobile design principles', 'completed' => false],
            ['title' => 'App Publishing', 'description' => 'Deploy to app stores', 'completed' => false],
        ],
        'DevOps Engineering' => [
            ['title' => 'Linux & Command Line', 'description' => 'Master Linux fundamentals and shell scripting', 'completed' => false],
            ['title' => 'Version Control (Git)', 'description' => 'Learn Git workflows and branching strategies', 'completed' => false],
            ['title' => 'CI/CD Pipelines', 'description' => 'Build automated deployment pipelines', 'completed' => false],
            ['title' => 'Containerization', 'description' => 'Docker and container orchestration', 'completed' => false],
            ['title' => 'Infrastructure as Code', 'description' => 'Terraform, Ansible, and automation', 'completed' => false],
            ['title' => 'Monitoring & Logging', 'description' => 'System monitoring and observability', 'completed' => false],
        ],
    ];
    
    return $milestones_map[$career_path] ?? $milestones_map['Web Development'];
}

function get_default_resources($milestone_title) {
    $resources_map = [
        'HTML Fundamentals' => [
            ['title' => 'HTML Crash Course', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
            ['title' => 'HTML Full Tutorial', 'url' => 'https://www.youtube.com/results?search_query=html+tutorial', 'platform' => 'YouTube'],
            ['title' => 'MDN HTML Guide', 'url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML', 'platform' => 'MDN Web Docs'],
        ],
        'CSS Mastery' => [
            ['title' => 'CSS Complete Guide', 'url' => 'https://www.udemy.com/topic/css/', 'platform' => 'Udemy'],
            ['title' => 'CSS Flexbox & Grid', 'url' => 'https://www.coursera.org/courses?query=css', 'platform' => 'Coursera'],
            ['title' => 'CSS Tricks', 'url' => 'https://css-tricks.com/', 'platform' => 'CSS-Tricks'],
        ],
        'JavaScript Basics' => [
            ['title' => 'JavaScript Tutorial', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
            ['title' => 'Modern JavaScript', 'url' => 'https://javascript.info/', 'platform' => 'JavaScript.info'],
            ['title' => 'JS Fundamentals', 'url' => 'https://www.udemy.com/topic/javascript/', 'platform' => 'Udemy'],
        ],
        'Frontend Frameworks' => [
            ['title' => 'React Tutorial', 'url' => 'https://react.dev/learn', 'platform' => 'React Docs'],
            ['title' => 'Vue.js Guide', 'url' => 'https://vuejs.org/guide/', 'platform' => 'Vue Docs'],
            ['title' => 'Frontend Masters', 'url' => 'https://frontendmasters.com/', 'platform' => 'Frontend Masters'],
        ],
        'Backend Development' => [
            ['title' => 'Node.js Tutorial', 'url' => 'https://nodejs.org/en/learn', 'platform' => 'Node.js'],
            ['title' => 'PHP Basics', 'url' => 'https://www.php.net/manual/en/tutorial.php', 'platform' => 'PHP.net'],
            ['title' => 'Backend Development', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
        ],
        'Database Management' => [
            ['title' => 'SQL Tutorial', 'url' => 'https://www.w3schools.com/sql/', 'platform' => 'W3Schools'],
            ['title' => 'MySQL Guide', 'url' => 'https://dev.mysql.com/doc/', 'platform' => 'MySQL Docs'],
            ['title' => 'Database Design', 'url' => 'https://www.coursera.org/courses?query=database', 'platform' => 'Coursera'],
        ],
        'Python Fundamentals' => [
            ['title' => 'Python Tutorial', 'url' => 'https://www.python.org/about/gettingstarted/', 'platform' => 'Python.org'],
            ['title' => 'Learn Python', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
            ['title' => 'Python Course', 'url' => 'https://www.coursera.org/courses?query=python', 'platform' => 'Coursera'],
        ],
        'Linux & Command Line' => [
            ['title' => 'Linux Command Line Basics', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
            ['title' => 'Linux Tutorial', 'url' => 'https://www.linux.org/forums/', 'platform' => 'Linux.org'],
            ['title' => 'Bash Scripting Guide', 'url' => 'https://www.gnu.org/software/bash/manual/', 'platform' => 'GNU'],
        ],
        'Version Control (Git)' => [
            ['title' => 'Git Tutorial', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
            ['title' => 'Git Documentation', 'url' => 'https://git-scm.com/doc', 'platform' => 'Git SCM'],
            ['title' => 'GitHub Learning', 'url' => 'https://skills.github.com/', 'platform' => 'GitHub'],
        ],
        'CI/CD Pipelines' => [
            ['title' => 'Jenkins Tutorial', 'url' => 'https://www.jenkins.io/doc/', 'platform' => 'Jenkins'],
            ['title' => 'GitHub Actions', 'url' => 'https://docs.github.com/en/actions', 'platform' => 'GitHub'],
            ['title' => 'CI/CD Best Practices', 'url' => 'https://www.udemy.com/topic/ci-cd/', 'platform' => 'Udemy'],
        ],
        'Containerization' => [
            ['title' => 'Docker Tutorial', 'url' => 'https://docs.docker.com/get-started/', 'platform' => 'Docker'],
            ['title' => 'Kubernetes Guide', 'url' => 'https://kubernetes.io/docs/tutorials/', 'platform' => 'Kubernetes'],
            ['title' => 'Container Basics', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
        ],
        'Infrastructure as Code' => [
            ['title' => 'Terraform Tutorial', 'url' => 'https://learn.hashicorp.com/terraform', 'platform' => 'HashiCorp'],
            ['title' => 'Ansible Guide', 'url' => 'https://docs.ansible.com/', 'platform' => 'Ansible'],
            ['title' => 'IaC Fundamentals', 'url' => 'https://www.coursera.org/courses?query=infrastructure', 'platform' => 'Coursera'],
        ],
        'Monitoring & Logging' => [
            ['title' => 'Prometheus & Grafana', 'url' => 'https://prometheus.io/docs/', 'platform' => 'Prometheus'],
            ['title' => 'ELK Stack Tutorial', 'url' => 'https://www.elastic.co/guide/', 'platform' => 'Elastic'],
            ['title' => 'Monitoring Best Practices', 'url' => 'https://www.udemy.com/topic/monitoring/', 'platform' => 'Udemy'],
        ],
    ];
    
    return $resources_map[$milestone_title] ?? [
        ['title' => 'General Course 1', 'url' => 'https://www.freecodecamp.org/learn', 'platform' => 'freeCodeCamp'],
        ['title' => 'General Course 2', 'url' => 'https://www.coursera.org/', 'platform' => 'Coursera'],
        ['title' => 'General Course 3', 'url' => 'https://www.udemy.com/', 'platform' => 'Udemy'],
    ];
}
?>