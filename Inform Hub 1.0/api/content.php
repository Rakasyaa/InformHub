<?php
// Informatika Hub - Content API
header('Content-Type: application/json');

// Get request parameters
$category = isset($_GET['category']) ? $_GET['category'] : 'html';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

// Validate parameters
$category = strtolower($category);
$lang = strtolower($lang);

// Define available categories
$validCategories = ['html', 'css', 'javascript', 'bootstrap', 'react', 'vue', 'blockchain', 'solidity', 'nft'];
if (!in_array($category, $validCategories)) {
    $category = 'html'; // Default to HTML if invalid
}

// Define available languages
$validLangs = ['en', 'id'];
if (!in_array($lang, $validLangs)) {
    $lang = 'en'; // Default to English if invalid
}

// Get content based on category and language
$content = getContent($category, $lang);

// Output JSON response
echo json_encode($content);

/**
 * Get content for a specific category and language
 * 
 * @param string $category The content category
 * @param string $lang The language code (en/id)
 * @return array The content data
 */
function getContent($category, $lang) {
    // Base content structure
    $content = [
        'title' => ucfirst($category),
        'description' => '',
        'sections' => [],
        'codeExample' => null
    ];
    
    // Check if it's a Web3 category
    if (in_array($category, ['blockchain', 'solidity', 'nft'])) {
        $content['category'] = 'web3';
    }
    
    // Generate content based on category and language
    switch ($category) {
        case 'html':
            if ($lang == 'en') {
                $content['description'] = 'Learn HTML to create the structure of web pages';
                $content['sections'] = [
                    [
                        'title' => 'Introduction to HTML',
                        'content' => '<p>HTML (HyperText Markup Language) is the standard markup language for creating web pages. HTML describes the structure of a web page semantically.</p>'
                    ],
                    [
                        'title' => 'Basic Structure',
                        'content' => '<p>Every HTML document has a basic structure that includes the following elements:</p>' .
                            '<ul><li><code>&lt;!DOCTYPE html&gt;</code> - Declares the document type</li>' .
                            '<li><code>&lt;html&gt;</code> - The root element</li>' .
                            '<li><code>&lt;head&gt;</code> - Contains meta information</li>' .
                            '<li><code>&lt;title&gt;</code> - Specifies the title</li>' .
                            '<li><code>&lt;body&gt;</code> - Contains the visible content</li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'HTML Example',
                    'description' => 'Try editing this HTML code and see the result',
                    'language' => 'html',
                    'code' => '<!DOCTYPE html>
<html>
<head>
    <title>My First Page</title>
</head>
<body>
    <h1>Hello World!</h1>
    <p>This is my first HTML page.</p>
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
    </ul>
</body>
</html>'
                ];
            } else {
                $content['description'] = 'Pelajari HTML untuk membuat struktur halaman web';
                $content['sections'] = [
                    [
                        'title' => 'Pengenalan HTML',
                        'content' => '<p>HTML (HyperText Markup Language) adalah bahasa markup standar untuk membuat halaman web. HTML menggambarkan struktur halaman web secara semantik.</p>'
                    ],
                    [
                        'title' => 'Struktur Dasar',
                        'content' => '<p>Setiap dokumen HTML memiliki struktur dasar yang mencakup elemen-elemen berikut:</p>' .
                            '<ul><li><code>&lt;!DOCTYPE html&gt;</code> - Mendeklarasikan tipe dokumen</li>' .
                            '<li><code>&lt;html&gt;</code> - Elemen root</li>' .
                            '<li><code>&lt;head&gt;</code> - Berisi informasi meta</li>' .
                            '<li><code>&lt;title&gt;</code> - Menentukan judul</li>' .
                            '<li><code>&lt;body&gt;</code> - Berisi konten yang terlihat</li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'Contoh HTML',
                    'description' => 'Coba edit kode HTML ini dan lihat hasilnya',
                    'language' => 'html',
                    'code' => '<!DOCTYPE html>
<html>
<head>
    <title>Halaman Pertama Saya</title>
</head>
<body>
    <h1>Halo Dunia!</h1>
    <p>Ini adalah halaman HTML pertama saya.</p>
    <ul>
        <li>Item 1</li>
        <li>Item 2</li>
        <li>Item 3</li>
    </ul>
</body>
</html>'
                ];
            }
            break;
            
        case 'css':
            if ($lang == 'en') {
                $content['description'] = 'Learn CSS to style and layout web pages';
                $content['sections'] = [
                    [
                        'title' => 'Introduction to CSS',
                        'content' => '<p>CSS (Cascading Style Sheets) is used to style and layout web pages. It controls the visual appearance of HTML elements.</p>'
                    ],
                    [
                        'title' => 'CSS Selectors',
                        'content' => '<p>CSS selectors are used to "find" (or select) the HTML elements you want to style.</p>' .
                            '<ul><li><code>element</code> - Selects all elements with the specified tag name</li>' .
                            '<li><code>#id</code> - Selects the element with the specified id</li>' .
                            '<li><code>.class</code> - Selects all elements with the specified class</li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'CSS Example',
                    'description' => 'Try editing this CSS code and see the result',
                    'language' => 'css',
                    'code' => 'body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
}

.css-demo-element {
    background-color: #3498db;
    color: white;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
    transition: all 0.3s ease;
}

.css-demo-element:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}'
                ];
            } else {
                $content['description'] = 'Pelajari CSS untuk mendesain dan mengatur tata letak halaman web';
                $content['sections'] = [
                    [
                        'title' => 'Pengenalan CSS',
                        'content' => '<p>CSS (Cascading Style Sheets) digunakan untuk mendesain dan mengatur tata letak halaman web. CSS mengontrol tampilan visual dari elemen HTML.</p>'
                    ],
                    [
                        'title' => 'Selektor CSS',
                        'content' => '<p>Selektor CSS digunakan untuk "menemukan" (atau memilih) elemen HTML yang ingin Anda desain.</p>' .
                            '<ul><li><code>element</code> - Memilih semua elemen dengan nama tag yang ditentukan</li>' .
                            '<li><code>#id</code> - Memilih elemen dengan id yang ditentukan</li>' .
                            '<li><code>.class</code> - Memilih semua elemen dengan class yang ditentukan</li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'Contoh CSS',
                    'description' => 'Coba edit kode CSS ini dan lihat hasilnya',
                    'language' => 'css',
                    'code' => 'body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 20px;
}

.css-demo-element {
    background-color: #3498db;
    color: white;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
    transition: all 0.3s ease;
}

.css-demo-element:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}'
                ];
            }
            break;
            
        case 'javascript':
            if ($lang == 'en') {
                $content['description'] = 'Learn JavaScript to create dynamic and interactive web pages';
                $content['sections'] = [
                    [
                        'title' => 'Introduction to JavaScript',
                        'content' => '<p>JavaScript is a programming language that enables interactive web pages. It is an essential part of web applications.</p>'
                    ],
                    [
                        'title' => 'JavaScript Syntax',
                        'content' => '<p>JavaScript syntax includes variables, operators, expressions, statements, and more:</p>' .
                            '<ul><li><code>var</code>, <code>let</code>, and <code>const</code> for declaring variables</li>' .
                            '<li>Functions for reusable code blocks</li>' .
                            '<li>Conditional statements like <code>if</code>, <code>else</code></li>' .
                            '<li>Loops like <code>for</code> and <code>while</code></li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'JavaScript Example',
                    'description' => 'Try editing this JavaScript code and see the result',
                    'language' => 'javascript',
                    'code' => '// Define a function to calculate factorial
function factorial(n) {
    if (n === 0 || n === 1) {
        return 1;
    } else {
        return n * factorial(n - 1);
    }
}

// Calculate and display factorials
for (let i = 1; i <= 5; i++) {
    console.log(`Factorial of ${i} is: ${factorial(i)}`);
}

// Return a message
return "Calculation complete!";'
                ];
            } else {
                $content['description'] = 'Pelajari JavaScript untuk membuat halaman web yang dinamis dan interaktif';
                $content['sections'] = [
                    [
                        'title' => 'Pengenalan JavaScript',
                        'content' => '<p>JavaScript adalah bahasa pemrograman yang memungkinkan halaman web interaktif. Ini adalah bagian penting dari aplikasi web.</p>'
                    ],
                    [
                        'title' => 'Sintaks JavaScript',
                        'content' => '<p>Sintaks JavaScript mencakup variabel, operator, ekspresi, pernyataan, dan lainnya:</p>' .
                            '<ul><li><code>var</code>, <code>let</code>, dan <code>const</code> untuk mendeklarasikan variabel</li>' .
                            '<li>Fungsi untuk blok kode yang dapat digunakan kembali</li>' .
                            '<li>Pernyataan kondisional seperti <code>if</code>, <code>else</code></li>' .
                            '<li>Loop seperti <code>for</code> dan <code>while</code></li></ul>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'Contoh JavaScript',
                    'description' => 'Coba edit kode JavaScript ini dan lihat hasilnya',
                    'language' => 'javascript',
                    'code' => '// Mendefinisikan fungsi untuk menghitung faktorial
function factorial(n) {
    if (n === 0 || n === 1) {
        return 1;
    } else {
        return n * factorial(n - 1);
    }
}

// Menghitung dan menampilkan faktorial
for (let i = 1; i <= 5; i++) {
    console.log(`Faktorial dari ${i} adalah: ${factorial(i)}`);
}

// Mengembalikan pesan
return "Perhitungan selesai!";'
                ];
            }
            break;
            
        case 'solidity':
            if ($lang == 'en') {
                $content['description'] = 'Learn Solidity to develop smart contracts for Ethereum blockchain';
                $content['sections'] = 
                [
                    [
                        'title' => 'Introduction to Solidity',
                        'content' => '<p>Solidity is an object-oriented programming language for writing smart contracts on Ethereum and compatible blockchains.</p>'
                    ],
                    [
                        'title' => 'Smart Contracts',
                        'content' => '<p>A smart contract is a program that runs on the Ethereum blockchain. It\'s a collection of code and data that resides at a specific address on the blockchain.</p>'
                    ]

                ];
                
                $content['codeExample'] = [
                    'title' => 'Solidity Example',
                    'description' => 'This is a simple Solidity smart contract example',
                    'language' => 'solidity',
                    'code' => '// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract SimpleStorage {
    uint256 private storedData;
    
    function set(uint256 x) public {
        storedData = x;
    }
    
    function get() public view returns (uint256) {
        return storedData;
    }
}'
                ];
            } else {
                $content['description'] = 'Pelajari Solidity untuk mengembangkan kontrak pintar untuk blockchain Ethereum';
                $content['sections'] = [
                    [
                        'title' => 'Pengenalan Solidity',
                        'content' => '<p>Solidity adalah bahasa pemrograman berorientasi objek untuk menulis kontrak pintar di Ethereum dan blockchain yang kompatibel.</p>'
                    ],
                    [
                        'title' => 'Kontrak Pintar',
                        'content' => '<p>Kontrak pintar adalah program yang berjalan di blockchain Ethereum. Ini adalah kumpulan kode dan data yang berada di alamat tertentu di blockchain.</p>'
                    ]
                ];
                
                $content['codeExample'] = [
                    'title' => 'Contoh Solidity',
                    'description' => 'Ini adalah contoh kontrak pintar Solidity sederhana',
                    'language' => 'solidity',
                    'code' => '// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract SimpleStorage {
    uint256 private storedData;
    
    function set(uint256 x) public {
        storedData = x;
    }
    
    function get() public view returns (uint256) {
        return storedData;
    }
}'
                ];
            }
            break;
            
        default:
            // Default content for other categories
            if ($lang == 'en') {
                $content['description'] = 'Learn about ' . ucfirst($category);
                $content['sections'] = [
                    [
                        'title' => 'Introduction to ' . ucfirst($category),
                        'content' => '<p>This section contains information about ' . $category . '. Content is coming soon!</p>'
                    ]
                ];
            } else {
                $content['description'] = 'Pelajari tentang ' . ucfirst($category);
                $content['sections'] = [
                    [
                        'title' => 'Pengenalan ' . ucfirst($category),
                        'content' => '<p>Bagian ini berisi informasi tentang ' . $category . '. Konten akan segera hadir!</p>'
                    ]
                ];
            }
    }
    
    return $content;
}
