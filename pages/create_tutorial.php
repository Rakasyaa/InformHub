<?php
require_once '../config/database.php';
require_once '../includes/user.php';
require_once '../includes/tutorial.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0">Buat Tutorial Baru</h2>
        </div>
        <div class="card-body">
            <form id="tutorialForm" method="POST" action="../includes/tutorial.php">
                <input type="hidden" name="action" value="create">

                <!-- Basic Information -->
                <div class="mb-4">
                    <h4 class="mb-3">Informasi Dasar</h4>
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Tutorial</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="category" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Singkat</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
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
                        <!-- Sections will be added here dynamically -->
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
                        <!-- Code examples will be added here dynamically -->
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= SITE_URL ?>/pages/tutorial.php" class="btn btn-secondary me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Tutorial</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                <input type="text" class="form-control section-title" name="sections[0][title]" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Konten</label>
                <textarea class="form-control section-content" name="sections[0][content]" rows="4" required></textarea>
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
                <label class="form-label">Kode</label>
                <pre><textarea class="form-control font-monospace" name="code_examples[0][code]" rows="6" style="font-family: 'Courier New', monospace;" required></textarea></pre>
            </div>
        </div>
    </div>
</template>

<script>
// Add a new section
let sectionCount = 0;
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
let exampleCount = 0;
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

// Add initial section and code example when the page loads
document.addEventListener('DOMContentLoaded', function() {
    addSection();
    addCodeExample();
});
</script>

<style>
/* Add some styling for better code display */
pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

pre textarea {
    width: 100%;
    min-height: 150px;
    border: none;
    background: transparent;
    resize: vertical;
    font-family: 'Courier New', Courier, monospace;
    line-height: 1.5;
}

.section-item, .code-example-item {
    border-left: 4px solid #0d6efd;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<?php
include_once '../includes/footer.php';
?>