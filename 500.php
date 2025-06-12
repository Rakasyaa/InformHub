<?php
/**
 * 500 Error page
 */
require_once 'config/config.php';

// Include header
include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto text-center">
        <div class="card">
            <div class="card-body">
                <h1 class="display-1 text-danger">500</h1>
                <h2 class="mb-4">Internal Server Error</h2>
                <p class="lead">Something went wrong on our end. Please try again later or contact the administrator if the problem persists.</p>
                <div class="mt-4">
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i> Go to Homepage
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>
