<?php
include 'header.php';

// Ambil postingan dari database
$sql = "SELECT posts.*, users.username, users.full_name, users.profile_picture 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE users.is_banned = 0
        ORDER BY posts.created_at DESC 
        LIMIT 20";
$stmt = $conn->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<div class="row">
    <!-- Sidebar Kiri -->
    <div class="col-md-3">
        <!-- Profil User -->
        <div class="card mb-3">
            <div class="card-body text-center">
                <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                     class="rounded-circle mb-2" width="80" height="80">
                <h6 class="fw-bold"><?php echo $current_user['full_name']; ?></h6>
                <p class="text-muted mb-0">@<?php echo $current_user['username']; ?></p>
                <?php if($current_user['bio']): ?>
                <p class="small text-muted mt-2"><?php echo $current_user['bio']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Menu -->
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Menu</h6>
                <a href="index.php" class="d-block py-2 text-dark text-decoration-none">
                    <i class="fas fa-home me-2"></i>Beranda
                </a>
                <a href="friends.php" class="d-block py-2 text-dark text-decoration-none">
                    <i class="fas fa-user-friends me-2"></i>Teman
                </a>
                <a href="groups.php" class="d-block py-2 text-dark text-decoration-none">
                    <i class="fas fa-users me-2"></i>Grup
                </a>
                <a href="reels.php" class="d-block py-2 text-dark text-decoration-none">
                    <i class="fas fa-play-circle me-2"></i>Reels
                </a>
                <a href="meme.php" class="d-block py-2 text-dark text-decoration-none">
                    <i class="fas fa-gamepad me-2"></i>Meme
                </a>
            </div>
        </div>
    </div>

    <!-- Konten Utama -->
    <div class="col-md-6">
        <!-- Buat Postingan -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <img src="assets/images/profiles/<?php echo $current_user['profile_picture']; ?>" 
                         class="rounded-circle me-3" width="50" height="50">
                    <button class="btn btn-light flex-grow-1 text-start py-2" 
                            data-bs-toggle="modal" data-bs-target="#createPostModal">
                        Apa yang Anda pikirkan, <?php echo $current_user['full_name']; ?>?
                    </button>
                </div>
            </div>
        </div>

        <!-- Daftar Postingan -->
        <?php if(count($posts) > 0): ?>
            <?php foreach($posts as $post): ?>
            <div class="card mb-3 post-card" data-post-id="<?php echo $post['id']; ?>">
                <div class="card-body">
                    <!-- Header Post -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                                 class="rounded-circle me-3" width="45" height="45">
                            <div>
                                <h6 class="fw-bold mb-0"><?php echo $post['full_name']; ?></h6>
                                <small class="text-muted">
                                    <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Konten Post -->
                    <p class="mb-3"><?php echo htmlspecialchars($post['content']); ?></p>
                    
                    <?php if($post['image']): ?>
                    <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                         class="img-fluid rounded mb-3" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>

                    <!-- Aksi Post -->
                    <div class="d-flex justify-content-between border-top pt-3">
                        <button class="btn btn-sm btn-outline-primary like-btn" data-post-id="<?php echo $post['id']; ?>">
                            <i class="far fa-thumbs-up"></i> Suka
                        </button>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="far fa-comment"></i> Komentar
                        </button>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="far fa-share-square"></i> Bagikan
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-feather fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada postingan</h5>
                    <p class="text-muted">Jadilah yang pertama membagikan sesuatu!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Kanan -->
    <div class="col-md-3">
        <!-- Kontak -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Kontak</h6>
                <?php
                // Ambil daftar teman
                $sql = "SELECT users.* FROM users 
                        JOIN friends ON (friends.user_id1 = users.id OR friends.user_id2 = users.id)
                        WHERE (friends.user_id1 = ? OR friends.user_id2 = ?) 
                        AND friends.status = 'accepted'
                        AND users.id != ?
                        LIMIT 8";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $user_id, $user_id]);
                $friends = $stmt->fetchAll();

                if(count($friends) > 0): 
                    foreach($friends as $friend): 
                ?>
                <div class="d-flex align-items-center mb-2">
                    <img src="assets/images/profiles/<?php echo $friend['profile_picture']; ?>" 
                         class="rounded-circle me-2" width="35" height="35">
                    <span class="small"><?php echo $friend['full_name']; ?></span>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                <p class="text-muted small">Belum ada teman</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Grup -->
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Grup Anda</h6>
                <?php
                // Ambil grup user (tambahkan backticks)
                $sql = "SELECT `groups`.* FROM `groups` 
                        JOIN group_members ON `groups`.id = group_members.group_id
                        WHERE group_members.user_id = ?
                        LIMIT 5";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id]);
                $groups = $stmt->fetchAll();

                if(count($groups) > 0): 
                    foreach($groups as $group): 
                ?>
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                         style="width: 35px; height: 35px;">
                        <i class="fas fa-users text-white small"></i>
                    </div>
                    <span class="small"><?php echo $group['name']; ?></span>
                </div>
                <?php 
                    endforeach;
                else: 
                ?>
                <p class="text-muted small">Belum bergabung dengan grup</p>
                <a href="groups.php" class="btn btn-primary btn-sm">Cari Grup</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Postingan -->
<div class="modal fade" id="createPostModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Postingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions/create_post.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="4" 
                                  placeholder="Apa yang Anda pikirkan?" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tambah Gambar (opsional)</label>
                        <input type="file" class="form-control" name="post_image" accept="image/*">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Posting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>