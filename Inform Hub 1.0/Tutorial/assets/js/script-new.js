// Informatika Hub - Main JavaScript

// Define valid categories for URL hash navigation and search
const validCategories = ['html', 'css', 'javascript', 'bootstrap', 'react', 'vue', 'blockchain', 'solidity', 'nft'];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const languageToggleBtn = document.getElementById('language-toggle-btn');
    const contentContainer = document.getElementById('content-container');
    const navLinks = document.querySelectorAll('.nav-link');
    const featuredLinks = document.querySelectorAll('.featured-link');
    const searchInput = document.getElementById('search-input');
    
    // Current state
    let currentTheme = localStorage.getItem('theme') || 'light';
    let currentLanguage = localStorage.getItem('language') || 'EN';
    let currentCategory = 'html'; // Default category
    
    // Initialize theme
    if (currentTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    // Initialize language
    languageToggleBtn.textContent = currentLanguage;
    
    // Sidebar toggle (for mobile)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            
            // Add overlay when sidebar is shown on mobile
            if (sidebar.classList.contains('show')) {
                const overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    document.body.removeChild(overlay);
                });
            } else {
                const existingOverlay = document.querySelector('.sidebar-overlay');
                if (existingOverlay) {
                    document.body.removeChild(existingOverlay);
                }
            }
        });
    }
    
    // Close sidebar when window is resized to desktop size
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            if (sidebar) {
                sidebar.classList.remove('show');
                const existingOverlay = document.querySelector('.sidebar-overlay');
                if (existingOverlay) {
                    document.body.removeChild(existingOverlay);
                }
            }
            
            // Refresh any CodeMirror instances on resize
            document.querySelectorAll('.CodeMirror').forEach(function(cm) {
                if (cm.CodeMirror) {
                    cm.CodeMirror.refresh();
                }
            });
        }
    });
    
    // Theme toggle
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            if (currentTheme === 'light') {
                document.body.setAttribute('data-theme', 'dark');
                themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                currentTheme = 'dark';
            } else {
                document.body.removeAttribute('data-theme');
                themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                currentTheme = 'light';
            }
            localStorage.setItem('theme', currentTheme);
            
            // Update CodeMirror themes if they exist
            document.querySelectorAll('.CodeMirror').forEach(function(cm) {
                if (cm.CodeMirror) {
                    cm.CodeMirror.setOption('theme', currentTheme === 'dark' ? 'monokai' : 'default');
                }
            });
        });
    }
    
    // Language toggle
    if (languageToggleBtn) {
        languageToggleBtn.addEventListener('click', function() {
            currentLanguage = currentLanguage === 'EN' ? 'ID' : 'EN';
            languageToggleBtn.textContent = currentLanguage;
            localStorage.setItem('language', currentLanguage);
            
            // Redirect to language.php to change language
            window.location.href = 'includes/language.php?lang=' + currentLanguage.toLowerCase();
        });
    }
    
    // Navigation links
    if (navLinks) {
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Only prevent default if it's a course page link
                if (this.getAttribute('data-category')) {
                    e.preventDefault();
                    
                    // Get category
                    const category = this.getAttribute('data-category');
                    
                    // Redirect to course page
                    window.location.href = 'course.php?category=' + category;
                    
                    // Close sidebar on mobile after clicking a link
                    if (window.innerWidth < 992 && sidebar) {
                        sidebar.classList.remove('show');
                        const existingOverlay = document.querySelector('.sidebar-overlay');
                        if (existingOverlay) {
                            document.body.removeChild(existingOverlay);
                        }
                    }
                }
            });
        });
    }
    
    // Featured content links (on homepage)
    if (featuredLinks) {
        featuredLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Let the link work normally - it now points directly to course.php
                // We don't need to prevent default
            });
        });
    }
    
    // Handle search functionality
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.toLowerCase().trim();
                if (searchTerm) {
                    // Simple search - redirect to the most relevant category
                    const categories = {
                        'html': ['html', 'tag', 'element', 'structure', 'markup'],
                        'css': ['css', 'style', 'design', 'color', 'layout'],
                        'javascript': ['javascript', 'js', 'function', 'variable', 'array', 'object'],
                        'bootstrap': ['bootstrap', 'grid', 'component', 'responsive'],
                        'react': ['react', 'component', 'hook', 'jsx', 'state'],
                        'vue': ['vue', 'directive', 'component', 'template'],
                        'blockchain': ['blockchain', 'bitcoin', 'ethereum', 'crypto', 'web3'],
                        'solidity': ['solidity', 'smart contract', 'ethereum', 'function'],
                        'nft': ['nft', 'token', 'collectible', 'digital asset']
                    };
                    
                    // Find the best matching category
                    let bestMatch = null;
                    let highestScore = 0;
                    
                    for (const [category, keywords] of Object.entries(categories)) {
                        let score = 0;
                        
                        // Check if search term contains the category name
                        if (searchTerm.includes(category)) {
                            score += 5;
                        }
                        
                        // Check for keyword matches
                        keywords.forEach(keyword => {
                            if (searchTerm.includes(keyword)) {
                                score += 1;
                            }
                        });
                        
                        if (score > highestScore) {
                            highestScore = score;
                            bestMatch = category;
                        }
                    }
                    
                    if (bestMatch) {
                        // Redirect to the course page with the best matching category
                        window.location.href = 'course.php?category=' + bestMatch;
                    } else {
                        // If no match, just redirect to HTML as default
                        window.location.href = 'course.php?category=html';
                    }
                }
            }
        });
    }
    
    // Initialize CodeMirror for code editors on the page
    initializeCodeEditors();
    
    // Function to initialize all code editors on the page
    function initializeCodeEditors() {
        const codeEditors = document.querySelectorAll('textarea[id^="code-editor"]');
        
        if (codeEditors.length > 0) {
            codeEditors.forEach(editor => {
                const language = editor.getAttribute('data-language') || 'html';
                const editorInstance = CodeMirror.fromTextArea(editor, {
                    lineNumbers: true,
                    mode: getEditorMode(language),
                    theme: currentTheme === 'dark' ? 'monokai' : 'default',
                    lineWrapping: true,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    indentUnit: 4,
                    tabSize: 4,
                    indentWithTabs: false,
                    extraKeys: {
                        "Tab": function(cm) {
                            cm.replaceSelection("    ", "end");
                        }
                    }
                });
                
                // Store the original code for reset functionality
                const originalCode = editor.value;
                editor.dataset.originalCode = originalCode;
                
                // Make editor responsive
                window.addEventListener('resize', function() {
                    editorInstance.refresh();
                });
            });
        }
        
        // Add event listeners for run and reset buttons
        const runButtons = document.querySelectorAll('[id^="run-code-btn"]');
        const resetButtons = document.querySelectorAll('[id^="reset-code-btn"]');
        
        runButtons.forEach(button => {
            button.addEventListener('click', function() {
                const editorId = this.getAttribute('data-editor');
                const language = this.getAttribute('data-language');
                const editor = document.querySelector(`#${editorId}`);
                
                if (editor && editor.CodeMirror) {
                    const code = editor.CodeMirror.getValue();
                    runCode(code, language);
                }
            });
        });
        
        resetButtons.forEach(button => {
            button.addEventListener('click', function() {
                const editorId = this.getAttribute('data-editor');
                const editor = document.querySelector(`#${editorId}`);
                
                if (editor && editor.CodeMirror) {
                    const originalCode = editor.dataset.originalCode || '';
                    editor.CodeMirror.setValue(originalCode);
                    
                    // Reset output
                    const outputId = this.getAttribute('data-output');
                    const outputContainer = document.querySelector(`#${outputId}`);
                    if (outputContainer) {
                        outputContainer.innerHTML = '<div class="output-placeholder">Output will appear here</div>';
                    }
                }
            });
        });
    }
    
    // Get editor mode based on language
    function getEditorMode(language) {
        switch (language.toLowerCase()) {
            case 'html':
                return 'htmlmixed';
            case 'css':
                return 'css';
            case 'javascript':
            case 'js':
                return 'javascript';
            case 'solidity':
                return 'javascript'; // Use JS mode for Solidity as fallback
            default:
                return 'htmlmixed';
        }
    }
    
    // Run code in the editor
    function runCode(code, language) {
        const outputContainer = document.getElementById('code-output');
        
        if (!outputContainer) {
            console.error('Output container not found');
            return;
        }
        
        // Show loading indicator
        outputContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        setTimeout(() => {
            switch (language.toLowerCase()) {
                case 'html':
                    // For HTML, create an iframe to render the code
                    outputContainer.innerHTML = '<iframe id="output-iframe" style="width:100%; height:200px; border:none; background-color: white;"></iframe>';
                    const iframe = document.getElementById('output-iframe');
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    iframeDoc.open();
                    iframeDoc.write(code);
                    iframeDoc.close();
                    break;
                    
                case 'css':
                    // For CSS, apply to a demo div
                    outputContainer.innerHTML = `
                        <style>${code}</style>
                        <div class="css-demo-container">
                            <div class="css-demo-element">CSS Demo Element</div>
                            <div class="css-demo-element">CSS Demo Element</div>
                        </div>
                    `;
                    break;
                    
                case 'javascript':
                case 'js':
                    // For JavaScript, try to execute and show output
                    outputContainer.innerHTML = '<div id="js-output" class="p-3"></div>';
                    try {
                        // Create a safe execution environment
                        const originalConsoleLog = console.log;
                        const logs = [];
                        
                        // Override console.log to capture output
                        console.log = function() {
                            logs.push(Array.from(arguments).join(' '));
                            originalConsoleLog.apply(console, arguments);
                        };
                        
                        // Execute the code
                        const result = new Function(code)();
                        
                        // Restore console.log
                        console.log = originalConsoleLog;
                        
                        // Display output
                        document.getElementById('js-output').innerHTML = `
                            ${logs.length ? '<h6>Console Output:</h6><pre class="bg-dark text-light p-3 rounded">' + logs.join('\n') + '</pre>' : ''}
                            ${result !== undefined ? '<h6>Return Value:</h6><pre class="bg-dark text-light p-3 rounded">' + result + '</pre>' : ''}
                        `;
                    } catch (error) {
                        document.getElementById('js-output').innerHTML = `<pre class="text-danger bg-light p-3 rounded">Error: ${error.message}</pre>`;
                    }
                    break;
                    
                case 'solidity':
                    // For Solidity, just show a message (can't execute Solidity directly in browser)
                    outputContainer.innerHTML = `
                        <div class="p-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Solidity code cannot be executed directly in the browser. In a real environment, this would be compiled and deployed to a blockchain.
                            </div>
                            <pre class="bg-dark text-light p-3 rounded">${code}</pre>
                        </div>
                    `;
                    break;
                    
                default:
                    outputContainer.innerHTML = '<div class="alert alert-warning m-3">Unsupported language</div>';
            }
        }, 500); // Add a small delay to show the loading indicator
    }
});
