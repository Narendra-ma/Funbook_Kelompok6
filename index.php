<?php
include 'header.php';
include 'class/Post.php';

$posts = Post::getAll($conn, 10, 0);
?>

<div class="row">
    <!-- Sidebar Kiri -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="sidebar">
            <div class="user-info mb-4 text-center">
                <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                     class="profile-img-lg mb-2">
                <h6 class="fw-bold mb-0"><?php echo $current_user['full_name']; ?></h6>
                <small class="text-muted">@<?php echo $current_user['username']; ?></small>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-home me-3"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user-friends me-3"></i>Teman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-bookmark me-3"></i>Tersimpan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users me-3"></i>Grup
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reels.php">
                        <i class="fas fa-play-circle me-3"></i>Reels
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="meme.php">
                        <i class="fas fa-gamepad me-3"></i>Meme
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Konten Utama -->
    <div class="col-lg-6 col-md-12">
        <!-- Buat Postingan -->
        <div class="create-post-card mb-4">
            <div class="d-flex align-items-center mb-3">
                <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                     class="profile-img me-3">
                <button class="btn btn-light w-100 text-start post-creator-btn" 
                        data-bs-toggle="modal" data-bs-target="#createPostModal">
                    Apa yang Anda pikirkan, <?php echo $current_user['full_name']; ?>?
                </button>
            </div>
        </div>

        <!-- Daftar Postingan -->
        <div id="posts-container">
            <?php foreach($posts as $post): 
                $isLiked = Post::isLiked($conn, $_SESSION['user_id'], $post['id']);
            ?>
            <div class="post-card mb-3" data-post-id="<?php echo $post['id']; ?>">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                             class="profile-img me-3">
                        <div>
                            <div class="fw-bold"><?php echo htmlspecialchars($post['full_name']); ?></div>
                            <div class="text-muted small"><?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                
                <div class="post-content mb-3">
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <?php if($post['image']): ?>
                    <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                         class="img-fluid rounded w-100 post-image">
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between text-muted small mb-2">
                    <div>
                        <i class="fas fa-thumbs-up text-primary"></i> <?php echo $post['likes_count']; ?>
                    </div>
                    <div>
                        <?php echo $post['comments_count']; ?> komentar
                    </div>
                </div>
                
                <div class="post-actions">
                    <button class="post-action-btn like-btn <?php echo $isLiked ? 'text-primary' : ''; ?>" 
                            data-post-id="<?php echo $post['id']; ?>">
                        <i class="<?php echo $isLiked ? 'fas' : 'far'; ?> fa-thumbs-up me-1"></i>Suka
                    </button>
                    <button class="post-action-btn comment-btn">
                        <i class="far fa-comment me-1"></i>Komentar
                    </button>
                    <button class="post-action-btn share-btn">
                        <i class="far fa-share-square me-1"></i>Bagikan
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Sidebar Kanan -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="sidebar">
            <!-- Sponsored -->
            <div class="sponsored-card mb-4">
                <h6 class="sidebar-title">Berita</h6>
                <div class="sponsored-item">
                    <div class="d-flex align-items-center mb-2">
                        <div class="sponsored-badge">17</div>
                        <div class="ms-2">
                            <div class="fw-bold small">Anak Bodoh masuk selokan</div>
                            <div class="text-muted x-small">katik.com</div>
                        </div>
                    </div>
                    <p class="x-small text-muted mb-2">mari kita jangan jadi anak itu</p>
                </div>
            </div>
            
            <!-- Kontak -->
            <div class="contacts-section">
                <h6 class="sidebar-title">Kontak</h6>
                <div class="contact-list">
                    <div class="contact-item">
                        <div class="position-relative">
                            <img src="https://ui-avatars.com/api/?name=Meta+AI&background=007bff&color=fff" 
                                 class="contact-img">
                            <span class="online-status bg-success"></span>
                        </div>
                        <span class="contact-name">Agus Prayogo</span>
                    </div>
                    <div class="contact-item">
                        <div class="position-relative">
                            <img src="https://ui-avatars.com/api/?name=Andi+P&background=28a745&color=fff" 
                                 class="contact-img">
                            <span class="online-status bg-success"></span>
                        </div>
                        <span class="contact-name">Andi Pratama</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Postingan -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Postingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions/create_post.php">
                    <div class="d-flex align-items-center mb-3">
                        <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                             class="profile-img me-3">
                        <div>
                            <div class="fw-bold"><?php echo $current_user['full_name']; ?></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4" 
                                  placeholder="Apa yang Anda pikirkan, <?php echo $current_user['full_name']; ?>?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" name="post_image" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Posting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>