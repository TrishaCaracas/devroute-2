// Toggle resources dropdown
function toggleResources(index) {
    const resourcesList = document.getElementById(`resources-${index}`);
    if (resourcesList) {
        resourcesList.classList.toggle('active');
        
        // Update button text
        const button = resourcesList.previousElementSibling;
        if (resourcesList.classList.contains('active')) {
            button.textContent = 'Hide Resources ▲';
        } else {
            button.textContent = 'View Resources ▼';
        }
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#EF4444';
                    
                    // Add error class if not already present
                    if (!field.classList.contains('error')) {
                        field.classList.add('error');
                    }
                } else {
                    field.style.borderColor = '#E5E7EB';
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Show error message
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Create or update error message
                let errorMsg = form.querySelector('.form-error-message');
                if (!errorMsg) {
                    errorMsg = document.createElement('div');
                    errorMsg.className = 'alert alert-error form-error-message';
                    errorMsg.innerHTML = '<p>Please fill in all required fields.</p>';
                    form.insertBefore(errorMsg, form.firstChild);
                }
            }
        });
        
        // Remove error styling on input
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.style.borderColor = '#E5E7EB';
                    this.classList.remove('error');
                }
            });
        });
    });
    
    // Vault modal handling
    const vaultModal = document.getElementById('vault-modal');
    const vaultForm = document.getElementById('vault-form');
    const openVaultModal = document.getElementById('open-vault-modal');
    const closeVaultModal = document.getElementById('close-vault-modal');
    const modalTitle = document.getElementById('vault-modal-title');
    const submitButton = document.getElementById('vault-submit-button');
    const fileInput = document.getElementById('certificate-file');
    const fileNameDisplay = document.getElementById('certificate-file-name');

    function resetFileDisplay(placeholderText = 'No file selected') {
        if (fileNameDisplay) {
            fileNameDisplay.value = '';
            fileNameDisplay.placeholder = placeholderText;
            fileNameDisplay.classList.add('is-placeholder');
        }
        if (fileInput) {
            fileInput.value = '';
        }
    }

    function openModal(mode = 'create', data = {}) {
        if (!vaultModal || !vaultForm) return;

        vaultModal.classList.add('active');
        document.body.classList.add('modal-open');

        vaultForm.reset();

        if (mode === 'create') {
            vaultForm.querySelector('input[name="action"]').value = 'create';
            vaultForm.querySelector('input[name="certificate_id"]').value = '';
            modalTitle.textContent = 'Upload Certificate';
            submitButton.textContent = 'Upload';
            if (fileInput) {
                fileInput.required = true;
            }
            resetFileDisplay('No file selected');
        } else {
            vaultForm.querySelector('input[name="action"]').value = 'update';
            vaultForm.querySelector('input[name="certificate_id"]').value = data.id || '';
            modalTitle.textContent = 'Update Certificate';
            submitButton.textContent = 'Save Changes';
            if (fileInput) {
                fileInput.required = false;
            }
            resetFileDisplay('Select a file to replace (optional)');
        }

        vaultForm.querySelector('#certificate-title').value = data.title || '';
        vaultForm.querySelector('#certificate-issuer').value = data.issuer || '';
        vaultForm.querySelector('#certificate-tag').value = data.tag || '';
        vaultForm.querySelector('#certificate-visibility').value = data.visibility || 'private';

        const issuedInput = vaultForm.querySelector('#certificate-issued-date');
        if (issuedInput) {
            issuedInput.value = data.issued_date || '';
        }
    }

    function closeModal() {
        if (!vaultModal) return;

        vaultModal.classList.remove('active');
        document.body.classList.remove('modal-open');
        if (vaultForm) {
            vaultForm.reset();
            vaultForm.querySelector('input[name="action"]').value = 'create';
            vaultForm.querySelector('input[name="certificate_id"]').value = '';
            modalTitle.textContent = 'Upload Certificate';
            submitButton.textContent = 'Upload';
            if (fileInput) {
                fileInput.required = true;
            }
        }
        resetFileDisplay('No file selected');
    }

    if (openVaultModal) {
        openVaultModal.addEventListener('click', () => openModal('create'));
    }

    if (closeVaultModal) {
        closeVaultModal.addEventListener('click', closeModal);
    }

    if (vaultModal) {
        vaultModal.addEventListener('click', (event) => {
            if (event.target === vaultModal) {
                closeModal();
            }
        });
    }

    const editButtons = document.querySelectorAll('.edit-certificate');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const card = button.closest('.certificate-card');
            if (!card) return;

            let data = {};
            try {
                data = JSON.parse(card.dataset.certificate || '{}');
            } catch (error) {
                console.error('Invalid certificate data', error);
            }

            openModal('update', data);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && vaultModal && vaultModal.classList.contains('active')) {
            closeModal();
        }
    });

    // Roadmap checklist and progress tracking
    const roadmapContainers = document.querySelectorAll('.roadmap-container[data-roadmap-id]');

    roadmapContainers.forEach(container => {
        const roadmapId = container.dataset.roadmapId;
        const checkboxes = container.querySelectorAll('.resource-checkbox');
        const progressFill = container.querySelector('.roadmap-progress .progress-fill');
        const progressPercentage = container.querySelector('.roadmap-progress .progress-percentage');
        const progressCount = container.querySelector('.roadmap-progress .progress-count');
        const storageKey = `roadmap-progress-${roadmapId}`;

        let storedState = {};
        try {
            const storedValue = localStorage.getItem(storageKey);
            storedState = storedValue ? JSON.parse(storedValue) : {};
        } catch (error) {
            storedState = {};
        }

        const updateProgress = () => {
            const total = checkboxes.length;
            const completed = Array.from(checkboxes).filter(cb => cb.checked).length;
            const percentage = total ? Math.round((completed / total) * 100) : 0;

            if (progressFill) {
                progressFill.style.width = `${percentage}%`;
            }

            if (progressPercentage) {
                progressPercentage.textContent = `${percentage}%`;
            }

            if (progressCount) {
                progressCount.textContent = total ? `${completed} of ${total} completed` : 'No resources yet';
            }

            container.classList.toggle('roadmap-complete', total > 0 && completed === total);
        };

        checkboxes.forEach(checkbox => {
            const resourceKey = checkbox.dataset.resourceKey;
            if (storedState[resourceKey]) {
                checkbox.checked = true;
            }

            const resourceItem = checkbox.closest('.resource-item');
            if (resourceItem) {
                resourceItem.classList.toggle('completed', checkbox.checked);
            }

            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    storedState[resourceKey] = true;
                } else {
                    delete storedState[resourceKey];
                }

                try {
                    localStorage.setItem(storageKey, JSON.stringify(storedState));
                } catch (error) {
                    // If localStorage is not available, fail silently
                }

                if (resourceItem) {
                    resourceItem.classList.toggle('completed', checkbox.checked);
                }

                updateProgress();
            });
        });

        updateProgress();
    });

    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                fileNameDisplay.value = fileInput.files[0].name;
                fileNameDisplay.classList.remove('is-placeholder');
            } else {
                resetFileDisplay(fileInput.required ? 'No file selected' : 'Select a file to replace (optional)');
            }
        });
        resetFileDisplay('No file selected');
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (!alert.classList.contains('form-error-message')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
    
    // Add animation to cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe cards for animation
    const cards = document.querySelectorAll('.domain-card, .feature-card, .roadmap-card, .milestone');
    cards.forEach(card => {
        observer.observe(card);
    });
    
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderColor = '#EF4444';
                
                // Show inline error
                let errorSpan = this.parentElement.querySelector('.inline-error');
                if (!errorSpan) {
                    errorSpan = document.createElement('span');
                    errorSpan.className = 'inline-error';
                    errorSpan.style.color = '#EF4444';
                    errorSpan.style.fontSize = '0.875rem';
                    errorSpan.style.marginTop = '0.25rem';
                    errorSpan.style.display = 'block';
                    errorSpan.textContent = 'Please enter a valid email address.';
                    this.parentElement.appendChild(errorSpan);
                }
            } else {
                this.style.borderColor = '#E5E7EB';
                const errorSpan = this.parentElement.querySelector('.inline-error');
                if (errorSpan) {
                    errorSpan.remove();
                }
            }
        });
    });
    
    // Password strength indicator (optional enhancement)
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        // Only add strength indicator on signup pages
        if (input.closest('form') && window.location.pathname.includes('signup')) {
            input.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                showPasswordStrength(this, strength);
            });
        }
    });
    
    // Mobile navigation toggle (if needed in future)
    const navToggle = document.querySelector('.nav-toggle');
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.resources-dropdown')) {
            const openDropdowns = document.querySelectorAll('.resources-list.active');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
                const button = dropdown.previousElementSibling;
                if (button) {
                    button.textContent = 'View Resources ▼';
                }
            });
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    const dropdowns = document.querySelectorAll('.resources-dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
});

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength;
}

// Show password strength indicator
function showPasswordStrength(input, strength) {
    let strengthIndicator = input.parentElement.querySelector('.password-strength');
    
    if (!strengthIndicator) {
        strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        strengthIndicator.style.marginTop = '0.5rem';
        input.parentElement.appendChild(strengthIndicator);
    }
    
    const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    const strengthColors = ['#EF4444', '#F59E0B', '#10B981', '#0496FF', '#006BA6'];
    
    if (input.value.length === 0) {
        strengthIndicator.innerHTML = '';
        return;
    }
    
    const strengthIndex = Math.min(strength, 4);
    strengthIndicator.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="flex: 1; height: 4px; background: #E5E7EB; border-radius: 2px; overflow: hidden;">
                <div style="width: ${(strengthIndex + 1) * 20}%; height: 100%; background: ${strengthColors[strengthIndex]}; transition: width 0.3s;"></div>
            </div>
            <span style="font-size: 0.875rem; color: ${strengthColors[strengthIndex]};">${strengthLevels[strengthIndex]}</span>
        </div>
    `;
}

// Assessment form handling
if (document.querySelector('.assessment-container')) {
    const assessmentForm = document.querySelector('.assessment-container form');
    
    if (assessmentForm) {
        // Save assessment data to sessionStorage for multi-step form
        assessmentForm.addEventListener('submit', function(e) {
            const formData = new FormData(this);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            // Store in sessionStorage
            const existingData = JSON.parse(sessionStorage.getItem('assessmentData') || '{}');
            const updatedData = { ...existingData, ...data };
            sessionStorage.setItem('assessmentData', JSON.stringify(updatedData));
        });
        
        // Load saved data on page load
        const savedData = JSON.parse(sessionStorage.getItem('assessmentData') || '{}');
        Object.keys(savedData).forEach(key => {
            const input = assessmentForm.querySelector(`[name="${key}"][value="${savedData[key]}"]`);
            if (input) {
                input.checked = true;
            }
        });
    }
}

// Milestone completion toggle (for future enhancement)
function toggleMilestoneComplete(milestoneId) {
    const milestone = document.querySelector(`[data-milestone-id="${milestoneId}"]`);
    if (milestone) {
        milestone.classList.toggle('completed');
        
        // Here you would typically send an AJAX request to update the database
        console.log(`Milestone ${milestoneId} toggled`);
    }
}

// Add loading state to buttons
function addLoadingState(button) {
    button.disabled = true;
    button.dataset.originalText = button.textContent;
    button.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></span> Loading...';
}

function removeLoadingState(button) {
    button.disabled = false;
    button.textContent = button.dataset.originalText;
}

// Add spin animation for loading
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Utility function to debounce events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Console welcome message
console.log('%cWelcome to DevRoute! 🚀', 'color: #006BA6; font-size: 20px; font-weight: bold;');
console.log('%cNavigate your tech career journey with confidence.', 'color: #0496FF; font-size: 14px;');