$(document).ready(function() {
    // Like functionality
    $('.like-btn').click(function() {
        const btn = $(this);
        const postId = btn.data('post-id');
        const icon = btn.find('i');
        
        $.ajax({
            url: 'actions/like_post.php',
            type: 'POST',
            data: { post_id: postId },
            success: function(response) {
                if(response === 'liked') {
                    icon.removeClass('far').addClass('fas text-primary');
                    btn.addClass('text-primary');
                } else {
                    icon.removeClass('fas text-primary').addClass('far');
                    btn.removeClass('text-primary');
                }
            }
        });
    });
});