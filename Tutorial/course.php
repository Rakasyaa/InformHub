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

// Include header and tutorial functions
include_once 'includes/header.php';
include_once 'includes/tutorial_functions.php';

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : 0;

// Get user role
$user_role = $user_logged_in ? get_user_role($user_id) : '';
$is_admin = $user_logged_in && $user_role === 'admin';
$is_moderator = $user_logged_in && ($user_role === 'moderator' || $user_role === 'admin');

// Get category from URL parameter
$category = isset($_GET['category']) ? $_GET['category'] : 'HTML';

// Validate category
$validCategories = ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'React', 'Vue', 'Blockchain', 'Solidity', 'NFT'];
if (!in_array($category, $validCategories)) {
    $category = 'HTML'; // Default to HTML if invalid
}

// Process comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_comment' && $user_logged_in) {
        $tutorial_id = isset($_POST['tutorial_id']) ? intval($_POST['tutorial_id']) : 0;
        $comment_content = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        
        if ($tutorial_id > 0 && !empty($comment_content)) {
            add_tutorial_comment($tutorial_id, $user_id, $comment_content);
        }
    } elseif ($_POST['action'] === 'toggle_pin' && $is_admin) {
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
        
        if ($comment_id > 0) {
            toggle_pin_comment($comment_id, $user_id);
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: course.php?category={$category}");
    exit;
}

// Get content from database
$content = get_tutorial_content($category);

// Get tutorial ID for comments
$tutorial_id = isset($content['id']) ? $content['id'] : 0;

// Get comments if tutorial exists in database
$comments = $tutorial_id > 0 ? get_tutorial_comments($tutorial_id) : [];

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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2><?php echo $section['title']; ?></h2>
            <?php if ($is_admin): ?>
            <div class="admin-actions">
                <a href="edit_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php echo $section['content']; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if ($is_admin): ?>
    <div class="card slide-in mb-4">
        <div class="card-header">
            <h2>Admin Actions</h2>
        </div>
        <div class="card-body">
            <a href="add_section.php?tutorial_id=<?php echo $tutorial_id; ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Section</a>
            <a href="edit_tutorial.php?id=<?php echo $tutorial_id; ?>" class="btn btn-info"><i class="fas fa-edit"></i> Edit Tutorial</a>
            <a href="delete_tutorial.php?id=<?php echo $tutorial_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this tutorial?')"><i class="fas fa-trash"></i> Delete Tutorial</a>
        </div>
    </div>
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
    
    <!-- Comments Section -->
    <div class="card slide-in mt-4">
        <div class="card-header">
            <h2>Comments</h2>
        </div>
        <div class="card-body">
            <?php if ($user_logged_in): ?>
            <!-- Comment Form -->
            <form method="post" action="course.php?category=<?php echo $category; ?>" class="mb-4">
                <input type="hidden" name="action" value="add_comment">
                <input type="hidden" name="tutorial_id" value="<?php echo $tutorial_id; ?>">
                <div class="form-group mb-3">
                    <label for="comment">Your Question or Comment:</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <?php else: ?>
            <div class="alert alert-info">
                <p>Please <a href="../Login/login.php">login</a> to leave a comment or ask a question.</p>
            </div>
            <?php endif; ?>
            
            <!-- Comments List -->
            <div class="comments-list">
                <h3 class="mb-3">Questions & Discussions</h3>
                
                <?php if (empty($comments)): ?>
                <div class="alert alert-secondary">
                    <p>No comments yet. Be the first to ask a question!</p>
                </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item <?php echo $comment['is_pinned'] ? 'pinned' : ''; ?>">
                        <div class="comment-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <?php if ($comment['is_pinned']): ?>
                                <span class="badge bg-primary ms-2"><i class="fas fa-thumbtack"></i> Pinned</span>
                                <?php endif; ?>
                                <small class="text-muted ms-2"><?php echo date('M d, Y g:i A', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <?php if ($is_admin): ?>
                            <div class="admin-actions">
                                <form method="post" action="course.php?category=<?php echo $category; ?>" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_pin">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <?php echo $comment['is_pinned'] ? '<i class="fas fa-thumbtack"></i> Unpin' : '<i class="fas fa-thumbtack"></i> Pin'; ?>
                                    </button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="comment-content mt-2">
                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- NFT Badge Section (for Web3 content) -->
    <?php if ($isWeb3 && $user_logged_in): ?>
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
            case 'HTML':
                return 'htmlmixed';
            case 'CSS':
                return 'css';   
            case 'JavaScript':
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
            case 'HTML':
                // For HTML, create an iframe to render the code
                outputContainer.innerHTML = '<iframe id="output-iframe" style="width:100%; height:200px; border:none;"></iframe>';
                const iframe = document.getElementById('output-iframe');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(code);
                iframeDoc.close();
                break;
                
            case 'CSS':
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
// include_once '../includes/footer.php';
?>
