// Nubilux Main JavaScript

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    Nubilux.init();
});

// Main Nubilux object
window.Nubilux = {
    // Configuration
    config: {
        apiUrl: '/api/v1',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        debug: false
    },
    
    // Initialize application
    init: function() {
        this.setupCSRF();
        this.setupForms();
        this.setupModals();
        this.setupTooltips();
        this.setupAlerts();
        console.log('Nubilux initialized');
    },
    
    // Setup CSRF protection
    setupCSRF: function() {
        const token = this.config.csrfToken;
        if (token) {
            // Add CSRF token to all AJAX requests
            const xhr = XMLHttpRequest.prototype;
            const originalOpen = xhr.open;
            xhr.open = function(method, url, ...args) {
                originalOpen.apply(this, [method, url, ...args]);
                if (method.toUpperCase() === 'POST') {
                    this.setRequestHeader('X-CSRF-Token', token);
                }
            };
        }
    },
    
    // Setup form handling
    setupForms: function() {
        document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
            form.addEventListener('submit', this.handleAjaxForm.bind(this));
        });
        
        // Setup form validation
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    },
    
    // Handle AJAX form submissions
    handleAjaxForm: function(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Show loading state
        this.setLoading(submitBtn, true);
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Send request
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.setLoading(submitBtn, false);
            
            if (data.success) {
                this.showAlert('success', data.message || 'Handling vellykket!');
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1000);
                }
            } else {
                this.showAlert('danger', data.message || 'En feil oppstod');
                if (data.errors) {
                    this.showValidationErrors(form, data.errors);
                }
            }
        })
        .catch(error => {
            this.setLoading(submitBtn, false);
            this.showAlert('danger', 'En uventet feil oppstod');
            console.error('Form submission error:', error);
        });
    },
    
    // Setup modal handling
    setupModals: function() {
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', function(event) {
                const target = this.getAttribute('data-bs-target');
                const modal = document.querySelector(target);
                if (modal) {
                    // Clear any previous content if it's a dynamic modal
                    if (this.hasAttribute('data-url')) {
                        const url = this.getAttribute('data-url');
                        Nubilux.loadModalContent(modal, url);
                    }
                }
            });
        });
    },
    
    // Load modal content via AJAX
    loadModalContent: function(modal, url) {
        const modalBody = modal.querySelector('.modal-body');
        modalBody.innerHTML = '<div class="text-center"><div class="spinner"></div></div>';
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
                // Re-initialize form handlers for new content
                this.setupForms();
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="alert alert-danger">Kunne ikke laste innhold</div>';
                console.error('Modal content loading error:', error);
            });
    },
    
    // Setup tooltips
    setupTooltips: function() {
        // Bootstrap tooltips
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    },
    
    // Setup alert auto-dismiss
    setupAlerts: function() {
        document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
            const delay = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined') {
                    const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                    alertInstance.close();
                } else {
                    alert.remove();
                }
            }, delay);
        });
    },
    
    // Utility functions
    setLoading: function(element, loading) {
        if (loading) {
            element.classList.add('btn-loading');
            element.disabled = true;
        } else {
            element.classList.remove('btn-loading');
            element.disabled = false;
        }
    },
    
    showAlert: function(type, message, container = null) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const targetContainer = container || document.querySelector('.alert-container') || document.querySelector('.main-content');
        if (targetContainer) {
            targetContainer.insertAdjacentHTML('afterbegin', alertHtml);
        }
    },
    
    showValidationErrors: function(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Show new errors
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errors[field];
                input.parentNode.appendChild(feedback);
            }
        });
    },
    
    // API helper
    api: function(endpoint, options = {}) {
        const url = this.config.apiUrl + endpoint;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (this.config.csrfToken) {
            defaultOptions.headers['X-CSRF-Token'] = this.config.csrfToken;
        }
        
        return fetch(url, { ...defaultOptions, ...options })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            });
    },
    
    // Module loader
    loadModule: function(moduleName) {
        return this.api(`/modules/${moduleName}/load`, { method: 'POST' });
    },
    
    // Debug helper
    log: function(...args) {
        if (this.config.debug) {
            console.log('[Nubilux]', ...args);
        }
    }
};