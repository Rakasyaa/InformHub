<?php
/**
 * Informatika Hub - Course Page
 * 
 * This file displays course content for a specific category.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include header
include_once 'includes/header.php';

// Get category from URL parameter
$category = isset($_GET['category']) ? $_GET['category'] : 'html';

// Validate category
$validCategories = ['html', 'css', 'javascript', 'bootstrap', 'react', 'vue', 'blockchain', 'solidity', 'nft'];
if (!in_array($category, $validCategories)) {
    $category = 'html'; // Default to HTML if invalid
}

// Get content from API
$apiUrl = "../api/content.php?category={$category}&lang={$current_language}";

// Safely get content with error handling
try {
    $jsonContent = file_get_contents($apiUrl);
    if ($jsonContent === false) {
        throw new Exception("Could not load content from API");
    }
    $content = json_decode($jsonContent, true);
    if (!is_array($content)) {
        throw new Exception("Invalid content format");
    }
} catch (Exception $e) {
    // Fallback content if API fails
    $content = [
        'title' => ucfirst($category),
        'description' => 'Learn about ' . ucfirst($category),
        'sections' => [],
        'category' => in_array($category, ['blockchain', 'solidity', 'nft']) ? 'web3' : 'frontend'
    ];
}

// Check if it's a Web3 category
$isWeb3 = isset($content['category']) && $content['category'] == 'web3';
?>

<!-- Course Content -->
<div class="course-container fade-in">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><?php echo translate('home'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo isset($content['title']) ? $content['title'] : ucfirst($category); ?></li>
        </ol>
    </nav>
    
    <!-- Course Header -->
    <div class="content-header slide-in">
        <h1><?php echo isset($content['title']) ? $content['title'] : ucfirst($category); ?></h1>
        <p class="lead"><?php echo isset($content['description']) ? $content['description'] : 'Learn about ' . ucfirst($category); ?></p>
    </div>
    
    <!-- Course Sections -->
    <?php if (isset($content['sections']) && is_array($content['sections'])): ?>
    <?php foreach ($content['sections'] as $section): ?>
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2><?php echo $section['title']; ?></h2>
        </div>
        <div class="card-body">
            <?php echo $section['content']; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Code Editor -->
    <?php if (isset($content['codeExample'])): ?>
    <div class="card slide-in">
        <div class="card-header">
            <h2><?php echo translate('try_it_yourself'); ?></h2>
        </div>
        <div class="card-body">
            <p><?php echo $content['codeExample']['description']; ?></p>
            <div class="code-editor-container">
                <div class="editor-header">
                    <div class="editor-title">
                        <i class="fas fa-code"></i>
                        <span><?php echo $content['codeExample']['title']; ?></span>
                    </div>
                    <div class="editor-actions">
                        <button id="run-code-btn" title="<?php echo translate('run_code'); ?>"><i class="fas fa-play"></i></button>
                        <button id="reset-code-btn" title="<?php echo translate('reset_code'); ?>"><i class="fas fa-undo"></i></button>
                    </div>
                </div>
                <div class="editor-content">
                    <textarea id="code-editor"><?php echo $content['codeExample']['code']; ?></textarea>
                </div>
                <div class="output-container" id="code-output">
                    <div class="output-placeholder"><?php echo translate('output'); ?> <?php echo ($current_language == 'en') ? 'will appear here' : 'akan muncul di sini'; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- NFT Badge Section (for Web3 content) -->
    <?php if ($isWeb3): ?>
    <div class="card slide-in mt-4">
        <div class="card-header">
            <h2><?php echo translate('nft_badges'); ?></h2>
        </div>
        <div class="card-body">
            <p><?php echo translate('complete_to_earn'); ?></p>
            <div class="progress-container">
                <div class="d-flex justify-content-between mb-1">
                    <span><?php echo translate('progress'); ?></span>
                    <span>60%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="nft-collection">
                <div class="nft-item">HTML</div>
                <div class="nft-item">CSS</div>
                <div class="nft-item" style="opacity: 0.5;">JS</div>
                <div class="nft-item" style="opacity: 0.5;">Web3</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Initialize CodeMirror -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize editor if it exists
    const codeEditorElement = document.getElementById('code-editor');
    if (codeEditorElement) {
        const editor = CodeMirror.fromTextArea(codeEditorElement, {
            lineNumbers: true,
            mode: getEditorMode('<?php echo $content['codeExample']['language']; ?>'),
            theme: 'monokai',
            lineWrapping: true,
            autoCloseBrackets: true,
            matchBrackets: true
        });
        
        // Store original code for reset
        const originalCode = `<?php echo str_replace('`', '\`', $content['codeExample']['code']); ?>`;
        
        // Run button
        document.getElementById('run-code-btn').addEventListener('click', function() {
            const currentCode = editor.getValue();
            runCode(currentCode, '<?php echo $content['codeExample']['language']; ?>');
        });
        
        // Reset button
        document.getElementById('reset-code-btn').addEventListener('click', function() {
            editor.setValue(originalCode);
            document.getElementById('code-output').innerHTML = '<div class="output-placeholder"><?php echo translate('output'); ?> <?php echo ($current_language == 'en') ? 'will appear here' : 'akan muncul di sini'; ?></div>';
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
        
        switch (language.toLowerCase()) {
            case 'html':
                // For HTML, create an iframe to render the code
                outputContainer.innerHTML = '<iframe id="output-iframe" style="width:100%; height:200px; border:none;"></iframe>';
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
                outputContainer.innerHTML = '<div id="js-output"></div>';
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
                        ${logs.length ? '<h6>Console Output:</h6><pre>' + logs.join('\n') + '</pre>' : ''}
                        ${result !== undefined ? '<h6>Return Value:</h6><pre>' + result + '</pre>' : ''}
                    `;
                } catch (error) {
                    document.getElementById('js-output').innerHTML = `<pre class="text-danger">Error: ${error.message}</pre>`;
                }
                break;
                
            case 'solidity':
                // For Solidity, just show a message (can't execute Solidity directly in browser)
                outputContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo ($current_language == 'en') ? 'Solidity code cannot be executed directly in the browser. In a real environment, this would be compiled and deployed to a blockchain.' : 'Kode Solidity tidak dapat dijalankan langsung di browser. Dalam lingkungan nyata, ini akan dikompilasi dan di-deploy ke blockchain.'; ?>
                    </div>
                    <pre>${code}</pre>
                `;
                break;
                
            default:
                outputContainer.innerHTML = '<div class="alert alert-warning">Unsupported language</div>';
        }
    }
});
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>
