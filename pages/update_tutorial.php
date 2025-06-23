<?php
require_once '../config/database.php';
require_once '../includes/user.php';
require_once '../includes/tutorial.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin/moderator
if (!isset($_SESSION['user_id']) || !isModerator()) {
    $_SESSION['error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini';
    header('Location: ' . SITE_URL . '/pages/login.php');
    exit;
}

// Get tutorial ID
$tutorialId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$tutorialId) {
    $_SESSION['error'] = 'Tutorial tidak ditemukan';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

// Get tutorial data
$tutorial = getTutorialById($tutorialId);
if (!$tutorial) {
    $_SESSION['error'] = 'Tutorial tidak ditemukan';
    header('Location: ' . SITE_URL . '/pages/tutorial.php');
    exit;
}

// Get sections and code examples
$sections = getTutorialSections($tutorialId);
$codeExamples = getTutorialCodeExamples($tutorialId);

$page_title = 'Edit Tutorial: ' . htmlspecialchars($tutorial['title']);
include_once '../includes/header.php';
?>

<!-- Sama seperti create_tutorial.php tapi dengan data yang sudah ada -->
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0"><?= htmlspecialchars($page_title) ?></h2>
        </div>
        <div class="card-body">
            <form id="tutorialForm" method="POST" action="../includes/tutorial.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="tutorial_id" value="<?= $tutorialId ?>">

                <!-- Basic Information -->
                <div class="mb-4">
                    <h4 class="mb-3">Informasi Dasar</h4>
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Tutorial</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($tutorial['title']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="category" name="category"
                               value="<?= htmlspecialchars($tutorial['category']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" id="description" name="description" 
                                 rows="3" required><?= htmlspecialchars($tutorial['description']) ?></textarea>
                    </div>
                </div>

                <!-- Sections -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Bagian Materi</h4>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addSection()">
                            <i class="fas fa-plus"></i> Tambah Bagian
                        </button>
                    </div>
                    
                    <div id="sectionsContainer">
                        <?php foreach ($sections as $index => $section): ?>
                        <div class="section-item card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5>Bagian <?= $index + 1 ?></h5>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeSection(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Judul</label>
                                    <input type="text" class="form-control" 
                                           name="sections[<?= $index ?>][title]" 
                                           value="<?= htmlspecialchars($section['title']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Konten</label>
                                    <textarea class="form-control" 
                                              name="sections[<?= $index ?>][content]" 
                                              rows="4" required><?= htmlspecialchars($section['content']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Code Examples -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Contoh Kode</h4>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addCodeExample()">
                            <i class="fas fa-plus"></i> Tambah Contoh Kode
                        </button>
                    </div>
                    
                    <div id="codeExamplesContainer">
                        <?php foreach ($codeExamples as $index => $example): ?>
                        <div class="code-example-item card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5>Contoh Kode <?= $index + 1 ?></h5>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeCodeExample(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Judul</label>
                                    <input type="text" class="form-control" 
                                           name="code_examples[<?= $index ?>][title]" 
                                           value="<?= htmlspecialchars($example['title']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kode</label>
                                    <pre><textarea class="form-control font-monospace" 
                                                 name="code_examples[<?= $index ?>][code]" 
                                                 rows="6" 
                                                 style="font-family: 'Courier New', monospace;" 
                                                 required><?= htmlspecialchars($example['code']) ?></textarea></pre>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= SITE_URL ?>/pages/tutorial.php?id=<?= $tutorialId ?>" class="btn btn-secondary me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Tutorial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sertakan JavaScript yang sama dengan create_tutorial.php -->
<script>
// Add a new section
let sectionCount = <?= count($sections) ?>;
function addSection() {
    const container = document.getElementById('sectionsContainer');
    const template = document.getElementById('sectionTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update the index for the new section
    const sectionNumber = container.children.length + 1;
    clone.querySelector('.section-number').textContent = sectionNumber;
    
    // Update the input names with the correct index
    const inputs = clone.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, `[${sectionCount}]`);
    });
    
    container.appendChild(clone);
    sectionCount++;
}

// Remove a section
function removeSection(button) {
    if (confirm('Apakah Anda yakin ingin menghapus bagian ini?')) {
        button.closest('.section-item').remove();
        // Renumber remaining sections
        const sections = document.querySelectorAll('.section-item');
        sections.forEach((section, index) => {
            section.querySelector('.section-number').textContent = index + 1;
        });
    }
}

// Add a new code example
let exampleCount = <?= count($codeExamples) ?>;
function addCodeExample() {
    const container = document.getElementById('codeExamplesContainer');
    const template = document.getElementById('codeExampleTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update the index for the new example
    const exampleNumber = container.children.length + 1;
    clone.querySelector('.example-number').textContent = exampleNumber;
    
    // Update the input names with the correct index
    const inputs = clone.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, `[${exampleCount}]`);
    });
    
    container.appendChild(clone);
    exampleCount++;
}

// Remove a code example
function removeCodeExample(button) {
    if (confirm('Apakah Anda yakin ingin menghapus contoh kode ini?')) {
        button.closest('.code-example-item').remove();
        // Renumber remaining examples
        const examples = document.querySelectorAll('.code-example-item');
        examples.forEach((example, index) => {
            example.querySelector('.example-number').textContent = index + 1;
        });
    }
}
</script>

<!-- Section Template (Hidden) -->
<template id="sectionTemplate">
    <div class="section-item card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5>Bagian <span class="section-number">1</span></h5>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSection(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="sections[0][title]" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konten</label>
                <textarea class="form-control" name="sections[0][content]" rows="4" required></textarea>
            </div>
        </div>
    </div>
</template>

<!-- Code Example Template (Hidden) -->
<template id="codeExampleTemplate">
    <div class="code-example-item card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5>Contoh Kode <span class="example-number">1</span></h5>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCodeExample(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="code_examples[0][title]" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="code_examples[0][description]" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Bahasa Pemrograman</label>
                <select class="form-select" name="code_examples[0][language]">
                    <option value="php">PHP</option>
                    <option value="javascript">JavaScript</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                    <option value="sql">SQL</option>
                    <option value="python">Python</option>
                    <option value="java">Java</option>
                    <option value="csharp">C#</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Kode</label>
                <pre><textarea class="form-control font-monospace" name="code_examples[0][code]" rows="6" style="font-family: 'Courier New', monospace;" required></textarea></pre>
            </div>
        </div>
    </div>
</template>

<?php
include_once '../includes/footer.php';
?>