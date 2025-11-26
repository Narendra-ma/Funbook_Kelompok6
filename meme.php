<?php
include 'header.php';
include 'class/Post.php';

// Get meme posts (posts with images)
$meme_posts = Post::getMemePosts($conn, 20);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-gamepad me-2"></i>Meme & Hiburan</h4>
        </div>
        
        <div class="row" id="meme-container">
            <?php if(count($meme_posts) > 0): ?>
                <?php foreach($meme_posts as $post): 
                    $isLiked = Post::isLiked($conn, $_SESSION['user_id'], $post['id']);
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="meme-card card h-100">
                        <?php if($post['image']): ?>
                        <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                             class="card-img-top meme-image" style="height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                                         class="profile-img-sm me-2">
                                    <small><?php echo htmlspecialchars($post['full_name']); ?></small>
                                </div>
                                <button class="btn btn-sm <?php echo $isLiked ? 'btn-primary' : 'btn-outline-primary'; ?> like-btn" 
                                        data-post-id="<?php echo $post['id']; ?>">
                                    <i class="<?php echo $isLiked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                    <span class="ms-1"><?php echo $post['likes_count']; ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-laugh-beam fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada meme</h5>
                    <p class="text-muted">Jadilah yang pertama membagikan meme lucu!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>