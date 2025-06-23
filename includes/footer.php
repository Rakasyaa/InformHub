    </div>
    <!-- End Main Content -->
    
    <!-- Logout Modal -->
    <div id="logoutModal" class="modal-overlay" hidden>
    <div class="modal-content">
        <p>Are you sure you want to logout?</p>
        <div class="modal-buttons">
        <button id="cancelLogout">Cancel</button>
        <a id="confirmLogout" href="#" class="confirm-button">Yes, logout</a>
        </div>
    </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Informatika Hub</h5>
                    <p>
                        A learning forum platform where users can share knowledge, 
                        ask questions, and engage in discussions about various learning topics.
                    </p>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Links</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="text-dark">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/forum.php" class="text-dark">Forum</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/search.php" class="text-dark">Search Topics</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="<?php echo SITE_URL; ?>/pages/profile.php" class="text-dark">Profile</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo SITE_URL; ?>/pages/login.php" class="text-dark">Login</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/pages/register.php" class="text-dark">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Popular Topics</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="<?php echo SITE_URL; ?>/pages/topic.php?id=1" class="text-dark">HTML</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/topic.php?id=2" class="text-dark">CSS</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/topic.php?id=3" class="text-dark">JavaScript</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/topic.php?id=4" class="text-dark">PHP</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2025 Informatika Hub
        </div>
    </footer>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
