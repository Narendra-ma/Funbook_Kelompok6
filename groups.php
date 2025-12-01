<?php
include 'header.php';

// Ambil grup yang diikuti user (tambahkan backticks)
$sql = "SELECT g.*, gm.role FROM `groups` g 
        JOIN group_members gm ON g.id = gm.group_id 
        WHERE gm.user_id = ? 
        ORDER BY g.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$user_groups = $stmt->fetchAll();

// Ambil grup yang disarankan (tambahkan backticks)
$sql = "SELECT g.*, COUNT(gm.id) as member_count 
        FROM `groups` g 
        LEFT JOIN group_members gm ON g.id = gm.group_id 
        WHERE g.id NOT IN (
            SELECT group_id FROM group_members WHERE user_id = ?
        )
        GROUP BY g.id 
        ORDER BY member_count DESC 
        LIMIT 6";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$suggested_groups = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>Grup
                </h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    <i class="fas fa-plus me-1"></i>Buat Grup Baru
                </button>
            </div>
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#your-groups">
                            Grup Anda (<?php echo count($user_groups); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#discover">
                            Temukan Grup
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Grup Anda -->
                    <div class="tab-pane fade show active" id="your-groups">
                        <?php if(count($user_groups) > 0): ?>
                        <div class="row">
                            <?php foreach($user_groups as $group): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <!-- Cover Grup -->
                                        <div class="bg-primary rounded mb-3 text-center text-white py-4">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($group['name']); ?></h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($group['description']); ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-dark small">
                                                <i class="fas fa-user me-1"></i><?php echo $group['role']; ?>
                                            </span>
                                            <span class="badge bg-info text-dark small">
                                                <i class="fas fa-users me-1"></i>
                                                <?php 
                                                // Hitung jumlah anggota
                                                $sql_member = "SELECT COUNT(*) as total FROM group_members WHERE group_id = ?";
                                                $stmt_member = $conn->prepare($sql_member);
                                                $stmt_member->execute([$group['id']]);
                                                $member_count = $stmt_member->fetch()['total'];
                                                echo $member_count; 
                                                ?> anggota
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showGroupDetail(<?php echo $group['id']; ?>, '<?php echo htmlspecialchars($group['name']); ?>', '<?php echo htmlspecialchars($group['description']); ?>', '<?php echo $group['role']; ?>')">
                                                Lihat Grup
                                            </button>
                                            <?php if($group['role'] != 'admin'): ?>
                                            <form method="POST" action="actions/leave_group.php" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari grup ini?')">
                                                <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="fas fa-sign-out-alt me-1"></i>Keluar Grup
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="fas fa-crown me-1"></i>Admin
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum bergabung dengan grup</h5>
                            <p class="text-muted">Bergabunglah dengan grup untuk terhubung dengan komunitas</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                                Buat Grup Pertama Anda
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Temukan Grup -->
                    <div class="tab-pane fade" id="discover">
                        <?php if(count($suggested_groups) > 0): ?>
                        <div class="row">
                            <?php foreach($suggested_groups as $group): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <!-- Cover Grup -->
                                        <div class="bg-success rounded mb-3 text-center text-white py-4">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($group['name']); ?></h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($group['description']); ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-dark small">
                                                <i class="fas fa-users me-1"></i><?php echo $group['member_count']; ?> anggota
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <form method="POST" action="actions/join_group.php">
                                            <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-user-plus me-1"></i>Gabung Grup
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada grup yang ditemukan</h5>
                            <p class="text-muted">Coba buat grup baru</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Grup -->
<div class="modal fade" id="createGroupModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Grup Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions/create_group.php">
                    <div class="mb-3">
                        <label class="form-label">Nama Grup</label>
                        <input type="text" class="form-control" name="group_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Deskripsikan grup Anda..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Buat Grup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Grup -->
<div class="modal fade" id="groupDetailModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupDetailTitle">Detail Grup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Postingan Grup</h6>
                            </div>
                            <div class="card-body">
                                <!-- Form Buat Postingan -->
                                <div class="mb-3">
                                    <textarea class="form-control" rows="3" placeholder="Apa yang ingin Anda bagikan di grup ini?"></textarea>
                                </div>
                                <div class="text-end mb-3">
                                    <button class="btn btn-primary btn-sm">Posting</button>
                                </div>
                                
                                <!-- Contoh Postingan -->
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0">John Doe</h6>
                                            <small class="text-muted">2 jam yang lalu</small>
                                        </div>
                                    </div>
                                    <p class="mb-2">Halo semuanya! Selamat datang di grup ini. Mari kita berdiskusi dengan baik dan saling membantu.</p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-heart me-1"></i>Suka (5)
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-comment me-1"></i>Komentar (3)
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-center py-3">
                                    <p class="text-muted">Fitur lengkap akan tersedia di halaman detail grup</p>
                                    <a href="#" class="btn btn-outline-primary btn-sm" id="fullDetailLink">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka Halaman Detail Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Info Grup</h6>
                            </div>
                            <div class="card-body">
                                <p id="groupDetailDescription"></p>
                                <div class="mb-2">
                                    <small class="text-muted">Role Anda:</small>
                                    <br>
                                    <span class="badge bg-primary" id="groupDetailRole"></span>
                                </div>
                                <hr>
                                <h6 class="small">Anggota Grup (12)</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark">John Doe (Admin)</span>
                                    <span class="badge bg-light text-dark">Jane Smith</span>
                                    <span class="badge bg-light text-dark">Bob Johnson</span>
                                    <span class="badge bg-light text-dark">+9 lainnya</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-primary" id="fullGroupLink">
                    <i class="fas fa-external-link-alt me-1"></i>Buka Halaman Lengkap
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showGroupDetail(groupId, groupName, groupDescription, userRole) {
    // Set data ke modal
    document.getElementById('groupDetailTitle').textContent = groupName;
    document.getElementById('groupDetailDescription').textContent = groupDescription;
    document.getElementById('groupDetailRole').textContent = userRole;
    
    // Set link untuk halaman detail lengkap
    const fullDetailLink = `group_detail.php?group_id=${groupId}`;
    document.getElementById('fullDetailLink').href = fullDetailLink;
    document.getElementById('fullGroupLink').href = fullDetailLink;
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('groupDetailModal'));
    modal.show();
}
</script>

<?php include 'footer.php'; ?>