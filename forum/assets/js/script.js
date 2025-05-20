/**
 * Main JavaScript file for Learning Forum
 */

// Document ready function
$(document).ready(function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Post voting functionality
    $('.post-vote-btn').click(function(e) {
        e.preventDefault();
        
        const postId = $(this).data('post-id');
        const voteType = $(this).data('vote-type');
        
        $.ajax({
            url: siteUrl + '/pages/vote_post.php',
            type: 'POST',
            data: {
                post_id: postId,
                vote_type: voteType
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update vote count
                    $('#post-' + postId + '-votes').text(response.voteCount);
                    
                    // Update button styles
                    if (response.userVote === 'upvote') {
                        $('#post-' + postId + '-upvote').addClass('upvoted');
                        $('#post-' + postId + '-downvote').removeClass('downvoted');
                    } else if (response.userVote === 'downvote') {
                        $('#post-' + postId + '-upvote').removeClass('upvoted');
                        $('#post-' + postId + '-downvote').addClass('downvoted');
                    } else {
                        $('#post-' + postId + '-upvote').removeClass('upvoted');
                        $('#post-' + postId + '-downvote').removeClass('downvoted');
                    }
                } else {
                    // Show error message
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Comment voting functionality
    $('.comment-vote-btn').click(function(e) {
        e.preventDefault();
        
        const commentId = $(this).data('comment-id');
        const voteType = $(this).data('vote-type');
        
        $.ajax({
            url: siteUrl + '/pages/vote_comment.php',
            type: 'POST',
            data: {
                comment_id: commentId,
                vote_type: voteType
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update vote count
                    $('#comment-' + commentId + '-votes').text(response.voteCount);
                    
                    // Update button styles
                    if (response.userVote === 'upvote') {
                        $('#comment-' + commentId + '-upvote').addClass('upvoted');
                        $('#comment-' + commentId + '-downvote').removeClass('downvoted');
                    } else if (response.userVote === 'downvote') {
                        $('#comment-' + commentId + '-upvote').removeClass('upvoted');
                        $('#comment-' + commentId + '-downvote').addClass('downvoted');
                    } else {
                        $('#comment-' + commentId + '-upvote').removeClass('upvoted');
                        $('#comment-' + commentId + '-downvote').removeClass('downvoted');
                    }
                } else {
                    // Show error message
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Follow/unfollow topic functionality
    $('.follow-topic-btn').click(function(e) {
        e.preventDefault();
        
        const topicId = $(this).data('topic-id');
        const action = $(this).hasClass('btn-primary') ? 'unfollow' : 'follow';
        
        $.ajax({
            url: siteUrl + '/pages/follow_topic.php',
            type: 'POST',
            data: {
                topic_id: topicId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update button text and style
                    if (action === 'follow') {
                        $('.follow-topic-btn')
                            .removeClass('btn-outline-primary')
                            .addClass('btn-primary')
                            .html('<i class="fas fa-check me-1"></i> Following');
                    } else {
                        $('.follow-topic-btn')
                            .removeClass('btn-primary')
                            .addClass('btn-outline-primary')
                            .html('<i class="fas fa-plus me-1"></i> Follow');
                    }
                    
                    // Update followers count
                    $('#topic-followers-count').text(response.followersCount);
                } else {
                    // Show error message
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Show reply form when reply button is clicked
    $('.reply-btn').click(function(e) {
        e.preventDefault();
        
        const commentId = $(this).data('comment-id');
        $('#reply-form-' + commentId).toggle();
    });
    
    // Preview image before upload
    $('#post-media').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#media-preview').html('<img src="' + e.target.result + '" class="img-fluid mt-2 rounded" />');
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Character counter for post content
    $('#post-content').on('input', function() {
        const maxLength = 5000;
        const currentLength = $(this).val().length;
        const remainingChars = maxLength - currentLength;
        
        $('#char-count').text(remainingChars + ' characters remaining');
        
        if (remainingChars < 0) {
            $('#char-count').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-danger');
        }
    });
});
