// menu login
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('navbarDropdown');
    const menu = document.getElementById('dropdownMenu');

    // Sembunyikan menu saat awal load
    menu.style.display = 'none';

    // Toggle show/hide saat klik username
    toggle.addEventListener('click', function (e) {
        e.preventDefault(); // agar tidak scroll ke atas
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function (e) {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
});

// alert
document.addEventListener("DOMContentLoaded", function () {
    // Tangkap semua tombol close
    const closeButtons = document.querySelectorAll('.btn-close');

    closeButtons.forEach(function(button) {
        button.addEventListener('click', function () {
            const alert = this.closest('.alert');
            if (alert) {
                // Tambahkan efek fade-out manual
                alert.classList.remove('show');
                alert.classList.add('fade');

                // Hapus dari DOM setelah efek transisi
                setTimeout(() => {
                    alert.remove();
                }, 300); // Sesuaikan dengan waktu transisi CSS
            }
        });
    });
});

// delate comment
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');

    document.querySelectorAll('.delete-comment').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const deleteUrl = this.getAttribute('data-delete-url');
            confirmBtn.setAttribute('href', deleteUrl);
            modal.removeAttribute('hidden');
        });
    });

    cancelBtn.addEventListener('click', function () {
        modal.setAttribute('hidden', true);
    });

    // Klik di luar modal
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.setAttribute('hidden', true);
        }
    });
});

// delate post
document.addEventListener('DOMContentLoaded', function () {
    // Elemen delete post
    const deletePostModal = document.getElementById('deletePostModal');
    const cancelDeletePost = document.getElementById('cancelDeletePost');
    const confirmDeletePost = document.getElementById('confirmDeletePost');
    
    // Tangani klik tombol delete post
    document.querySelectorAll('.delete-post-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const deleteUrl = this.getAttribute('data-delete-url');
            confirmDeletePost.setAttribute('href', deleteUrl);
            deletePostModal.removeAttribute('hidden');
        });
    });
    
    // Batalkan delete
    cancelDeletePost.addEventListener('click', function () {
        deletePostModal.setAttribute('hidden', true);
    });
    
    // Klik di luar modal
    deletePostModal.addEventListener('click', function (e) {
        if (e.target === deletePostModal) {
            deletePostModal.setAttribute('hidden', true);
        }
    });
});

// edit profil
document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openEditProfileBtn');
    const modal = document.getElementById('editProfileModal');
    const closeBtn = document.getElementById('closeEditProfileBtn');

    openBtn.addEventListener('click', () => {
        modal.removeAttribute('hidden');
    });

    closeBtn.addEventListener('click', () => {
        modal.setAttribute('hidden', true);
    });

    // Close modal when clicking outside the modal content
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.setAttribute('hidden', true);
        }
    });
});


// Log-out
document.addEventListener('DOMContentLoaded', function () {
    // Elemen logout
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogout = document.getElementById('cancelLogout');
    const confirmLogout = document.getElementById('confirmLogout');

    // Tangani klik tombol logout
    document.querySelectorAll('.logout-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const logoutUrl = this.getAttribute('data-logout-url');
            confirmLogout.setAttribute('href', logoutUrl);
            logoutModal.removeAttribute('hidden');
        });
    });

    // Batalkan logout
    cancelLogout.addEventListener('click', function () {
        logoutModal.setAttribute('hidden', true);
    });

    // Klik di luar modal logout
    logoutModal.addEventListener('click', function (e) {
        if (e.target === logoutModal) {
            logoutModal.setAttribute('hidden', true);
        }
    });
});


// Up-vote and down-vote (no jQuery / Bootstrap)
document.addEventListener('DOMContentLoaded', function () {
    // Voting for post
    document.querySelectorAll('.post-vote-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const postId = this.dataset.postId;
            const voteType = this.dataset.voteType;

            fetch(`${siteUrl}/pages/vote_post.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `post_id=${encodeURIComponent(postId)}&vote_type=${encodeURIComponent(voteType)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update vote count
                    const voteCountSpan = document.getElementById(`post-${postId}-votes`);
                    voteCountSpan.textContent = data.voteCount;

                    // Update button styles
                    updateVoteStyles(postId, data.userVote);
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
            });
        });
    });

    function updateVoteStyles(postId, userVote) {
        const upBtn = document.getElementById(`post-${postId}-upvote`);
        const downBtn = document.getElementById(`post-${postId}-downvote`);

        if (!upBtn || !downBtn) return;

        // Reset both buttons first
        upBtn.classList.remove('btn-success');
        upBtn.classList.add('btn-outline-success');

        downBtn.classList.remove('btn-danger');
        downBtn.classList.add('btn-outline-danger');

        // Apply vote style
        if (userVote === 'upvote') {
            upBtn.classList.remove('btn-outline-success');
            upBtn.classList.add('btn-success');
        } else if (userVote === 'downvote') {
            downBtn.classList.remove('btn-outline-danger');
            downBtn.classList.add('btn-danger');
        }
    }
});
