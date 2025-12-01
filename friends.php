<?php
include 'header.php';

// Ambil daftar teman
$sql = "SELECT u.* FROM users u 
        JOIN friends f ON (f.user_id1 = u.id OR f.user_id2 = u.id)
        WHERE (f.user_id1 = ? OR f.user_id2 = ?) 
        AND f.status = 'accepted'
        AND u.id != ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id]);
$friends = $stmt->fetchAll();

// Ambil permintaan pertemanan
$sql = "SELECT u.* FROM users u 
        JOIN friends f ON f.user_id1 = u.id
        WHERE f.user_id2 = ? AND f.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$friend_requests = $stmt->fetchAll();

// Ambil saran pertemanan
$sql = "SELECT u.* FROM users u 
        WHERE u.id != ? 
        AND u.id NOT IN (
            SELECT CASE 
                WHEN user_id1 = ? THEN user_id2 
                ELSE user_id1 
            END 
            FROM friends 
            WHERE user_id1 = ? OR user_id2 = ?
        )
        AND u.is_banned = 0
        ORDER BY RAND() 
        LIMIT 6";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$suggested_friends = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-friends me-2"></i>Teman
                </h4>
            </div>
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-friends">
                            Semua Teman (<?php echo count($friends); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#requests">
                            Permintaan 
                            <?php if(count($friend_requests) > 0): ?>
                            <span class="badge bg-danger"><?php echo count($friend_requests); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#suggestions">
                            Saran Teman
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Semua Teman -->
                    <div class="tab-pane fade show active" id="all-friends">
                        <?php if(count($friends) > 0): ?>
                        <div class="row">
                            <?php foreach($friends as $friend): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <img src="assets/images/profiles/<?php echo $friend['profile_picture']; ?>" 
                                             class="rounded-circle mb-3" width="100" height="100">
                                        <!-- PERUBAHAN: Nama teman jadi link ke profile -->
                                        <a href="profile.php?id=<?php echo $friend['id']; ?>" class="text-decoration-none text-dark">
                                            <h6 class="fw-bold"><?php echo $friend['full_name']; ?></h6>
                                        </a>
                                        <p class="text-muted small">@<?php echo $friend['username']; ?></p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="profile.php?id=<?php echo $friend['id']; ?>" 
                                               class="btn btn-primary btn-sm">Lihat Profil</a>
                                            <form method="POST" action="actions/remove_friend.php" class="d-inline">
                                                <input type="hidden" name="friend_id" value="<?php echo $friend['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada teman</h5>
                            <p class="text-muted">Mulai tambah teman untuk terhubung dengan mereka</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Permintaan Pertemanan -->
                    <div class="tab-pane fade" id="requests">
                        <?php if(count($friend_requests) > 0): ?>
                        <div class="row">
                            <?php foreach($friend_requests as $request): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="assets/images/profiles/<?php echo $request['profile_picture']; ?>" 
                                                 class="rounded-circle me-3" width="60" height="60">
                                            <div>
                                                <!-- PERUBAHAN: Nama request jadi link ke profile -->
                                                <a href="profile.php?id=<?php echo $request['id']; ?>" class="text-decoration-none text-dark">
                                                    <h6 class="fw-bold mb-0"><?php echo $request['full_name']; ?></h6>
                                                </a>
                                                <p class="text-muted small mb-0">@<?php echo $request['username']; ?></p>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <form method="POST" action="actions/accept_friend.php" class="flex-fill">
                                                <input type="hidden" name="friend_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-primary w-100">Konfirmasi</button>
                                            </form>
                                            <form method="POST" action="actions/delete_friend.php" class="flex-fill">
                                                <input type="hidden" name="friend_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-outline-secondary w-100">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada permintaan pertemanan</h5>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Saran Teman -->
                    <div class="tab-pane fade" id="suggestions">
                        <?php if(count($suggested_friends) > 0): ?>
                        <div class="row">
                            <?php foreach($suggested_friends as $suggested): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="assets/images/profiles/<?php echo $suggested['profile_picture']; ?>" 
                                                 class="rounded-circle me-3" width="60" height="60">
                                            <div class="flex-grow-1">
                                                <!-- PERUBAHAN: Nama saran teman jadi link ke profile -->
                                                <a href="profile.php?id=<?php echo $suggested['id']; ?>" class="text-decoration-none text-dark">
                                                    <h6 class="fw-bold mb-0"><?php echo $suggested['full_name']; ?></h6>
                                                </a>
                                                <p class="text-muted small mb-1">@<?php echo $suggested['username']; ?></p>
                                                <?php if($suggested['bio']): ?>
                                                <p class="small text-muted mb-0"><?php echo $suggested['bio']; ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <form method="POST" action="actions/add_friend.php">
                                            <input type="hidden" name="user_id" value="<?php echo $suggested['id']; ?>">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-user-plus me-1"></i>Tambah Teman
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada saran pertemanan</h5>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>