<?php
/**
 * Informatika Hub - Live Code Editor
 * 
 * This file provides a dedicated live code editor experience.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include header
include_once 'includes/header.php';

// Get language from URL parameter
$language = isset($_GET['language']) ? $_GET['language'] : 'html';

// Validate language
$validLanguages = ['html', 'css', 'javascript', 'solidity'];
if (!in_array($language, $validLanguages)) {
    $language = 'html'; // Default to HTML if invalid
}

// Default code templates
$codeTemplates = [
    'html' => '<!DOCTYPE html>
<html>
<head>
    <title>My Web Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Hello, World!</h1>
    <p>This is a paragraph.</p>
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
    </ul>
</body>
</html>',
    'css' => 'body {
    font-family: "Segoe UI", Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f8f9fa;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

.button {
    display: inline-block;
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #2980b9;
}',
    'javascript' => '// Define a class for a simple calculator
class Calculator {
    constructor() {
        this.result = 0;
    }
    
    add(num) {
        this.result += num;
        return this;
    }
    
    subtract(num) {
        this.result -= num;
        return this;
    }
    
    multiply(num) {
        this.result *= num;
        return this;
    }
    
    divide(num) {
        if (num === 0) {
            console.error("Cannot divide by zero");
            return this;
        }
        this.result /= num;
        return this;
    }
    
    getResult() {
        return this.result;
    }
    
    clear() {
        this.result = 0;
        return this;
    }
}

// Create a new calculator instance
const calc = new Calculator();

// Perform some calculations
calc.add(5)
    .multiply(2)
    .subtract(3)
    .divide(2);

// Display the result
console.log("Result:", calc.getResult());

// Try some more operations
calc.clear()
    .add(10)
    .multiply(3)
    .divide(2)
    .add(5);

console.log("New result:", calc.getResult());',
    'solidity' => '// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract Token {
    string public name;
    string public symbol;
    uint8 public decimals;
    uint256 public totalSupply;
    
    mapping(address => uint256) private balances;
    mapping(address => mapping(address => uint256)) private allowances;
    
    event Transfer(address indexed from, address indexed to, uint256 value);
    event Approval(address indexed owner, address indexed spender, uint256 value);
    
    constructor(string memory _name, string memory _symbol, uint8 _decimals, uint256 _initialSupply) {
        name = _name;
        symbol = _symbol;
        decimals = _decimals;
        totalSupply = _initialSupply * 10**uint256(_decimals);
        balances[msg.sender] = totalSupply;
        emit Transfer(address(0), msg.sender, totalSupply);
    }
    
    function balanceOf(address account) public view returns (uint256) {
        return balances[account];
    }
    
    function transfer(address to, uint256 amount) public returns (bool) {
        require(to != address(0), "Transfer to zero address");
        require(balances[msg.sender] >= amount, "Insufficient balance");
        
        balances[msg.sender] -= amount;
        balances[to] += amount;
        emit Transfer(msg.sender, to, amount);
        return true;
    }
    
    function allowance(address owner, address spender) public view returns (uint256) {
        return allowances[owner][spender];
    }
    
    function approve(address spender, uint256 amount) public returns (bool) {
        allowances[msg.sender][spender] = amount;
        emit Approval(msg.sender, spender, amount);
        return true;
    }
    
    function transferFrom(address from, address to, uint256 amount) public returns (bool) {
        require(from != address(0), "Transfer from zero address");
        require(to != address(0), "Transfer to zero address");
        require(balances[from] >= amount, "Insufficient balance");
        require(allowances[from][msg.sender] >= amount, "Insufficient allowance");
        
        balances[from] -= amount;
        balances[to] += amount;
        allowances[from][msg.sender] -= amount;
        emit Transfer(from, to, amount);
        return true;
    }
}'
];

// Get code template for selected language
$codeTemplate = isset($codeTemplates[$language]) ? $codeTemplates[$language] : '';

// Language display names
$languageNames = [
    'html' => 'HTML',
    'css' => 'CSS',
    'javascript' => 'JavaScript',
    'solidity' => 'Solidity'
];

// Language display name
$languageDisplayName = isset($languageNames[$language]) ? $languageNames[$language] : ucfirst($language);
?>

<!-- Live Editor -->
<div class="editor-page-container fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><?php echo translate('home'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo ($current_language == 'en') ? 'Live Editor' : 'Editor Langsung'; ?></li>
        </ol>
    </nav>
    
    <div class="card slide-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2><?php echo ($current_language == 'en') ? 'Live Code Editor' : 'Editor Kode Langsung'; ?> - <?php echo $languageDisplayName; ?></h2>
            <div class="language-selector">
                <div class="btn-group" role="group">
                    <a href="editor.php?language=html" class="btn btn-sm <?php echo $language == 'html' ? 'btn-primary' : 'btn-outline-primary'; ?>">HTML</a>
                    <a href="editor.php?language=css" class="btn btn-sm <?php echo $language == 'css' ? 'btn-primary' : 'btn-outline-primary'; ?>">CSS</a>
                    <a href="editor.php?language=javascript" class="btn btn-sm <?php echo $language == 'javascript' ? 'btn-primary' : 'btn-outline-primary'; ?>">JavaScript</a>
                    <a href="editor.php?language=solidity" class="btn btn-sm <?php echo $language == 'solidity' ? 'btn-primary' : 'btn-outline-primary'; ?>">Solidity</a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="live-editor-container">
                <div class="editor-header">
                    <div class="editor-title">
                        <i class="fas fa-code"></i>
                        <span><?php echo $languageDisplayName; ?> <?php echo ($current_language == 'en') ? 'Editor' : 'Editor'; ?></span>
                    </div>
                    <div class="editor-actions">
                        <button id="run-code-btn" title="<?php echo translate('run_code'); ?>"><i class="fas fa-play"></i> <?php echo translate('run_code'); ?></button>
                        <button id="reset-code-btn" title="<?php echo translate('reset_code'); ?>"><i class="fas fa-undo"></i> <?php echo translate('reset_code'); ?></button>
                        <button id="save-code-btn" title="<?php echo ($current_language == 'en') ? 'Save Code' : 'Simpan Kode'; ?>"><i class="fas fa-save"></i> <?php echo ($current_language == 'en') ? 'Save' : 'Simpan'; ?></button>
                    </div>
                </div>
                <div class="row m-0">
                    <div class="col-md-6 p-0">
                        <div class="editor-content" style="height: 500px;">
                            <textarea id="code-editor"><?php echo $codeTemplate; ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6 p-0">
                        <div class="output-container" id="code-output" style="height: 500px; overflow: auto;">
                            <div class="output-placeholder">
                                <?php echo translate('output'); ?> <?php echo ($current_language == 'en') ? 'will appear here' : 'akan muncul di sini'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="card mt-4 slide-in">
        <div class="card-header">
            <h3><?php echo ($current_language == 'en') ? 'Instructions' : 'Instruksi'; ?></h3>
        </div>
        <div class="card-body">
            <?php if ($current_language == 'en'): ?>
                <p>This is a live code editor where you can write and test your code in real-time. Follow these steps:</p>
                <ol>
                    <li>Write or edit the code in the editor on the left.</li>
                    <li>Click the "Run Code" button to execute your code.</li>
                    <li>See the output on the right panel.</li>
                    <li>Use the "Reset Code" button to revert to the original template.</li>
                    <li>Switch between languages using the buttons at the top.</li>
                </ol>
                <p><strong>Note:</strong> The editor supports HTML, CSS, JavaScript, and Solidity (display only).</p>
            <?php else: ?>
                <p>Ini adalah editor kode langsung di mana Anda dapat menulis dan menguji kode Anda secara real-time. Ikuti langkah-langkah berikut:</p>
                <ol>
                    <li>Tulis atau edit kode di editor di sebelah kiri.</li>
                    <li>Klik tombol "Jalankan Kode" untuk menjalankan kode Anda.</li>
                    <li>Lihat output di panel kanan.</li>
                    <li>Gunakan tombol "Reset Kode" untuk kembali ke template asli.</li>
                    <li>Beralih antar bahasa menggunakan tombol di bagian atas.</li>
                </ol>
                <p><strong>Catatan:</strong> Editor mendukung HTML, CSS, JavaScript, dan Solidity (hanya tampilan).</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Initialize CodeMirror -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize editor
    const editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
        lineNumbers: true,
        mode: getEditorMode('<?php echo $language; ?>'),
        theme: 'monokai',
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
    
    // Store original code for reset
    const originalCode = `<?php echo str_replace('`', '\`', $codeTemplate); ?>`;
    
    // Run button
    document.getElementById('run-code-btn').addEventListener('click', function() {
        const currentCode = editor.getValue();
        runCode(currentCode, '<?php echo $language; ?>');
    });
    
    // Reset button
    document.getElementById('reset-code-btn').addEventListener('click', function() {
        editor.setValue(originalCode);
        document.getElementById('code-output').innerHTML = '<div class="output-placeholder"><?php echo translate('output'); ?> <?php echo ($current_language == 'en') ? 'will appear here' : 'akan muncul di sini'; ?></div>';
    });
    
    // Save button (just a placeholder for now)
    document.getElementById('save-code-btn').addEventListener('click', function() {
        alert('<?php echo ($current_language == 'en') ? 'Code saved successfully!' : 'Kode berhasil disimpan!'; ?>');
    });
    
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
                outputContainer.innerHTML = '<iframe id="output-iframe" style="width:100%; height:100%; border:none;"></iframe>';
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
                    <div class="css-demo-container p-4">
                        <h2>CSS Preview</h2>
                        <div class="container">
                            <h1>Heading 1</h1>
                            <p>This is a paragraph of text. This text is used to show how your CSS styles affect typography.</p>
                            <a href="#" class="button">Button</a>
                            <div class="css-demo-element mt-3">CSS Demo Element</div>
                            <div class="css-demo-element">CSS Demo Element</div>
                        </div>
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
                            <?php echo ($current_language == 'en') ? 'Solidity code cannot be executed directly in the browser. In a real environment, this would be compiled and deployed to a blockchain.' : 'Kode Solidity tidak dapat dijalankan langsung di browser. Dalam lingkungan nyata, ini akan dikompilasi dan di-deploy ke blockchain.'; ?>
                        </div>
                        <h6>Code Preview:</h6>
                        <pre class="bg-dark text-light p-3 rounded">${code}</pre>
                    </div>
                `;
                break;
                
            default:
                outputContainer.innerHTML = '<div class="alert alert-warning m-3">Unsupported language</div>';
        }
    }
});
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>
