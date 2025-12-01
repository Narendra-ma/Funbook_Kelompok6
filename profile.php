<?php
include 'header.php';

// Ambil ID profil yang dilihat (jika tidak ada, gunakan profil sendiri)
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Ambil data profil
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$profile_id]);
$profile_user = $stmt->fetch();

if(!$profile_user){
    header("Location: index.php");
    exit;
}

// Cek apakah melihat profil sendiri
$is_own_profile = ($profile_id == $_SESSION['user_id']);

// Cek apakah user saat ini adalah admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

// Ambil postingan user
$sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$profile_id]);
$user_posts = $stmt->fetchAll();

// Cek status pertemanan
if(!$is_own_profile){
    $sql = "SELECT * FROM friends 
            WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $profile_id, $profile_id, $_SESSION['user_id']]);
    $friend_status = $stmt->fetch();
}
?>

<div class="row">
    <!-- Cover Photo -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body p-0">
                <!-- Cover Photo -->
                <div style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0; position: relative;">
                    <?php if($profile_user['cover_photo']): ?>
                    <img src="assets/images/covers/<?php echo $profile_user['cover_photo']; ?>" 
                         class="w-100 h-100" style="object-fit: cover; border-radius: 8px 8px 0 0;">
                    <?php endif; ?>
                </div>
                
                <!-- Profile Info -->
                <div class="text-center" style="margin-top: -75px; position: relative; z-index: 1;">
                    <img src="assets/images/profiles/<?php echo $profile_user['profile_picture']; ?>" 
                         class="rounded-circle border border-4 border-white mb-2" width="150" height="150">
                    <h3 class="fw-bold"><?php echo $profile_user['full_name']; ?></h3>
                    <p class="text-muted">@<?php echo $profile_user['username']; ?></p>
                    
                    <!-- Badge Status -->
                    <div class="mb-3">
                        <?php if($profile_user['is_admin']): ?>
                        <span class="badge bg-danger">Admin</span>
                        <?php endif; ?>
                        <?php if($profile_user['is_banned']): ?>
                        <span class="badge bg-dark">BANNED</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($profile_user['bio']): ?>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($profile_user['bio']); ?></p>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <?php if($is_own_profile): ?>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-1"></i>Edit Profil
                        </button>
                        <?php else: ?>
                            <?php if($friend_status): ?>
                                <?php if($friend_status['status'] == 'accepted'): ?>
                                <button class="btn btn-success">
                                    <i class="fas fa-check me-1"></i>Teman
                                </button>
                                <?php elseif($friend_status['user_id1'] == $_SESSION['user_id']): ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-clock me-1"></i>Permintaan Dikirim
                                </button>
                                <?php else: ?>
                                <form method="POST" action="actions/accept_friend.php" class="d-inline">
                                    <input type="hidden" name="friend_id" value="<?php echo $profile_user['id']; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-1"></i>Konfirmasi
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php else: ?>
                            <form method="POST" action="actions/add_friend.php" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i>Tambah Teman
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <!-- Tombol Admin Actions -->
                            <?php if($is_admin && !$profile_user['is_admin']): ?>
                                <?php if(!$profile_user['is_banned']): ?>
                                <!-- Tombol Ban untuk Admin -->
                                <form method="POST" action="actions/ban_user.php" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                                    <input type="hidden" name="redirect" value="profile">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm" 
                                            onclick="return confirm('Apakah Anda yakin ingin BAN user <?php echo htmlspecialchars($profile_user['full_name']); ?>?\n\nAlasan: Melanggar aturan komunitas')">
                                        <i class="fas fa-ban me-1"></i>Ban User
                                    </button>
                                </form>
                                <?php else: ?>
                                <!-- Tombol Unban untuk Admin -->
                                <form method="POST" action="actions/unban_user.php" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                                    <input type="hidden" name="redirect" value="profile">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check-circle me-1"></i>Unban User
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <!-- Tombol Jadikan Admin -->
                                <?php if(!$profile_user['is_admin']): ?>
                                <form method="POST" action="actions/make_admin.php" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                                    <input type="hidden" name="redirect" value="profile">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                                    <button type="submit" class="btn btn-info btn-sm" 
                                            onclick="return confirm('Apakah Anda yakin ingin menjadikan <?php echo htmlspecialchars($profile_user['full_name']); ?> sebagai admin?')">
                                        <i class="fas fa-user-shield me-1"></i>Jadikan Admin
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Konten Profil -->
    <div class="col-md-8">
        <?php if($profile_user['is_banned']): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>User ini telah dibanned.</strong> Akun ini tidak dapat melakukan postingan, komentar, atau interaksi lainnya.
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Postingan</h5>
            </div>
            <div class="card-body">
                <?php if(count($user_posts) > 0): ?>
                    <?php foreach($user_posts as $post): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="assets/images/profiles/<?php echo $profile_user['profile_picture']; ?>" 
                                         class="rounded-circle me-3" width="45" height="45">
                                    <div>
                                        <!-- PERUBAHAN: Nama di postingan profile jadi link ke profile -->
                                        <a href="profile.php?id=<?php echo $profile_user['id']; ?>" class="text-decoration-none text-dark">
                                            <h6 class="fw-bold mb-0"><?php echo $profile_user['full_name']; ?></h6>
                                        </a>
                                        <small class="text-muted">
                                            <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php if($is_admin): ?>
                                <!-- Tombol Hapus Postingan untuk Admin -->
                                <form method="POST" action="actions/delete_post_admin.php" 
                                      onsubmit="return confirm('Hapus postingan ini?')">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <input type="hidden" name="redirect" value="profile">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>

                            <p class="mb-3"><?php echo htmlspecialchars($post['content']); ?></p>
                            
                            <?php if($post['image']): ?>
                            <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                                 class="img-fluid rounded mb-3" style="max-height: 400px; object-fit: cover;">
                            <?php endif; ?>

                            <?php if(!$profile_user['is_banned']): ?>
                            <div class="d-flex justify-content-between border-top pt-3">
                                <button class="btn btn-sm btn-outline-primary like-btn" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="far fa-thumbs-up"></i> Suka
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='comment.php?post_id=<?php echo $post['id']; ?>'">
                                    <i class="far fa-comment"></i> Komentar
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="far fa-share-square"></i> Bagikan
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-feather fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada postingan</h5>
                        <?php if($is_own_profile): ?>
                        <p class="text-muted">Bagikan sesuatu untuk memulai!</p>
                        <?php else: ?>
                        <p class="text-muted">User ini belum membagikan postingan</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Profil -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Profil</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status Akun</small>
                    <div>
                        <?php if($profile_user['is_admin']): ?>
                        <span class="badge bg-danger">Administrator</span>
                        <?php elseif($profile_user['is_banned']): ?>
                        <span class="badge bg-dark">Banned</span>
                        <?php else: ?>
                        <span class="badge bg-success">Aktif</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Bergabung</small>
                    <div><?php echo date('d M Y', strtotime($profile_user['created_at'])); ?></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Email</small>
                    <div><?php echo $profile_user['email']; ?></div>
                </div>
                <?php if($profile_user['bio']): ?>
                <div class="mb-3">
                    <small class="text-muted">Bio</small>
                    <div><?php echo htmlspecialchars($profile_user['bio']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if($is_admin && !$profile_user['is_admin']): ?>
                <hr>
                <div class="mb-3">
                    <small class="text-muted">Admin Actions</small>
                    <div class="d-grid gap-2 mt-2">
                        <?php if(!$profile_user['is_banned']): ?>
                        <form method="POST" action="actions/ban_user.php">
                            <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                            <input type="hidden" name="redirect" value="profile">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <button type="submit" class="btn btn-warning w-100 btn-sm" 
                                    onclick="return confirm('Apakah Anda yakin ingin BAN user ini?\n\nAlasan: Melanggar aturan komunitas')">
                                <i class="fas fa-ban me-1"></i>Ban User
                            </button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="actions/unban_user.php">
                            <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                            <input type="hidden" name="redirect" value="profile">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <button type="submit" class="btn btn-success w-100 btn-sm">
                                <i class="fas fa-check-circle me-1"></i>Unban User
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="actions/make_admin.php">
                            <input type="hidden" name="user_id" value="<?php echo $profile_user['id']; ?>">
                            <input type="hidden" name="redirect" value="profile">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <button type="submit" class="btn btn-info w-100 btn-sm" 
                                    onclick="return confirm('Jadikan user ini sebagai admin?')">
                                <i class="fas fa-user-shield me-1"></i>Jadikan Admin
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<?php if($is_own_profile): ?>
<div class="modal fade" id="editProfileModal">
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

<!-- Modal Upload Video di Profile -->
<div class="modal fade" id="uploadVideoModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="actions/upload_reel.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Video ke Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="content_type" value="profile_video">
                    
                    <div class="mb-3">
                        <label class="form-label">Video</label>
                        <input type="file" name="video" class="form-control" 
                               accept="video/mp4,video/avi,video/mov,video/wmv" required>
                        <small class="text-muted">Format: MP4, AVI, MOV, WMV</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Caption/Deskripsi</label>
                        <textarea name="caption" class="form-control" rows="3" 
                                  placeholder="Tambahkan deskripsi video..."></textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="share_to_reels" id="shareToReels" checked>
                        <label class="form-check-label" for="shareToReels">
                            Tampilkan juga di halaman Reels
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload Video</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>