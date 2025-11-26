<?php
include 'header.php';
include 'class/User.php';
include 'class/Post.php';

// Get user profile data
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];
$profile_user = User::getById($conn, $user_id);

if(!$profile_user){
    header("Location: index.php");
    exit;
}

// Get user's posts
$user_posts = Post::getUserPosts($conn, $user_id);

// Check if current user is viewing their own profile
$is_own_profile = ($user_id == $_SESSION['user_id']);
?>

<div class="row">
    <!-- Cover Photo -->
    <div class="col-12">
        <div class="profile-cover mb-4">
            <div class="cover-photo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 300px; border-radius: 8px; position: relative;">
                <?php if($profile_user['cover_photo']): ?>
                <img src="assets/images/covers/<?php echo $profile_user['cover_photo']; ?>" 
                     class="w-100 h-100" style="object-fit: cover; border-radius: 8px;">
                <?php endif; ?>
            </div>
            <div class="profile-info text-center" style="margin-top: -75px;">
                <img src="assets/images/profiles/<?php echo $profile_user['profile_picture']; ?>" 
                     class="profile-img-xl mb-2">
                <h3 class="fw-bold"><?php echo htmlspecialchars($profile_user['full_name']); ?></h3>
                <p class="text-muted">@<?php echo htmlspecialchars($profile_user['username']); ?></p>
                <?php if($profile_user['bio']): ?>
                <p class="bio-text"><?php echo htmlspecialchars($profile_user['bio']); ?></p>
                <?php endif; ?>
                
                <?php if($is_own_profile): ?>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-1"></i>Edit Profil
                </button>
                <?php else: ?>
                <button class="btn btn-primary" id="addFriendBtn" data-user-id="<?php echo $profile_user['id']; ?>">
                    <i class="fas fa-user-plus me-1"></i>Tambah Teman
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Content -->
    <div class="col-lg-8">
        <div class="profile-posts">
            <h5 class="mb-3">Postingan</h5>
            <?php if(count($user_posts) > 0): ?>
                <?php foreach($user_posts as $post): 
                    $isLiked = Post::isLiked($conn, $_SESSION['user_id'], $post['id']);
                ?>
                <div class="post-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                                 class="profile-img me-3">
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($post['full_name']); ?></div>
                                <div class="text-muted small"><?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="post-content mb-3">
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <?php if($post['image']): ?>
                        <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                             class="img-fluid rounded w-100 post-image">
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-actions">
                        <button class="post-action-btn like-btn <?php echo $isLiked ? 'text-primary' : ''; ?>" 
                                data-post-id="<?php echo $post['id']; ?>">
                            <i class="<?php echo $isLiked ? 'fas' : 'far'; ?> fa-thumbs-up me-1"></i>Suka
                        </button>
                        <button class="post-action-btn comment-btn">
                            <i class="far fa-comment me-1"></i>Komentar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-feather fa-3x mb-3"></i>
                    <p>Belum ada postingan</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Profile Sidebar -->
    <div class="col-lg-4">
        <div class="profile-details">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Bergabung</small>
                        <div><?php echo date('d M Y', strtotime($profile_user['created_at'])); ?></div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <div><?php echo htmlspecialchars($profile_user['email']); ?></div>
                    </div>
                    <?php if($profile_user['bio']): ?>
                    <div class="mb-2">
                        <small class="text-muted">Bio</small>
                        <div><?php echo htmlspecialchars($profile_user['bio']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<?php if($is_own_profile): ?>
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions/update_profile.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" rows="3"><?php echo htmlspecialchars($profile_user['bio']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Cover</label>
                        <input type="file" class="form-control" name="cover_photo" accept="image/*">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>