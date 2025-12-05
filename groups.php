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
                                            <button type="button" class="btn btn-primary btn-sm" onclick="showGroupDetail(<?php echo $group['id']; ?>, '<?php echo htmlspecialchars(addslashes($group['name'])); ?>', '<?php echo htmlspecialchars(addslashes($group['description'])); ?>', '<?php echo $group['role']; ?>')">
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
                                <form id="groupPostForm">
                                    <input type="hidden" name="group_id" id="modalGroupId">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="content" id="postContent" 
                                                  rows="3" placeholder="Apa yang ingin Anda bagikan di grup ini?" required></textarea>
                                    </div>
                                    <div class="text-end mb-3">
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitPostBtn">
                                            <i class="fas fa-paper-plane me-1"></i>Posting
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Daftar Postingan Grup -->
                                <div id="groupPostsContainer">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2">Memuat postingan...</p>
                                    </div>
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
                                <h6 class="small">Anggota Grup</h6>
                                <div class="d-flex flex-wrap gap-2" id="groupMembersList">
                                    <span class="badge bg-light">Memuat...</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm" id="fullGroupLink">
                                        <i class="fas fa-external-link-alt me-1"></i>Buka Halaman Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.alert-temporary {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<script>
let currentGroupId = null;

function showGroupDetail(groupId, groupName, groupDescription, userRole) {
    console.log("Opening group detail:", groupId);
    
    // Set data ke modal
    currentGroupId = groupId;
    
    document.getElementById('groupDetailTitle').textContent = groupName;
    document.getElementById('groupDetailDescription').textContent = groupDescription;
    document.getElementById('groupDetailRole').textContent = userRole;
    document.getElementById('modalGroupId').value = groupId;
    
    // Set link untuk halaman detail lengkap
    const fullDetailLink = `group_detail.php?group_id=${groupId}`;
    document.getElementById('fullGroupLink').href = fullDetailLink;
    
    // Reset form
    document.getElementById('postContent').value = '';
    
    // Load postingan dan anggota via AJAX
    loadGroupPosts(groupId);
    loadGroupMembers(groupId);
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('groupDetailModal'));
    modal.show();
}

function loadGroupPosts(groupId) {
    console.log("Loading posts for group:", groupId);
    
    const container = document.getElementById('groupPostsContainer');
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2">Memuat postingan...</p>
        </div>
    `;
    
    // AJAX request untuk load postingan
    fetch(`actions/get_group_posts.php?group_id=${groupId}&_=${Date.now()}`)
        .then(response => {
            if(!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Posts response:", data);
            
            if (data.success) {
                displayPosts(data.posts);
            } else {
                showError(data.message || 'Gagal memuat postingan');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showError('Terjadi kesalahan jaringan. Coba refresh halaman.');
        });
}

function displayPosts(posts) {
    const container = document.getElementById('groupPostsContainer');
    
    if (!posts || posts.length === 0) {
        container.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-comment-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada postingan</p>
            <p class="text-muted small">Jadilah yang pertama berbagi di grup ini</p>
        </div>
        `;
        return;
    }
    
    let html = '';
    posts.forEach(post => {
        html += `
        <div class="border rounded p-3 mb-3" id="post-${post.id}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center">
                    <img src="assets/images/profiles/${post.profile_picture || 'default.jpg'}" 
                         class="rounded-circle me-2" width="35" height="35" 
                         onerror="this.src='assets/images/profiles/default.jpg'">
                    <div>
                        <h6 class="mb-0">${post.username}</h6>
                        <small class="text-muted">${post.time_ago}</small>
                    </div>
                </div>
            </div>
            <p class="mb-2">${post.content}</p>
            <div class="d-flex gap-2">
                <button class="btn btn-sm ${post.is_liked ? 'btn-danger' : 'btn-outline-danger'}" 
                        onclick="likeGroupPost(${post.id})">
                    <i class="fas fa-heart me-1"></i>Suka (${post.like_count || 0})
                </button>
                <button class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-comment me-1"></i>Komentar (${post.comment_count || 0})
                </button>
            </div>
        </div>
        `;
    });
    
    container.innerHTML = html;
}

function showError(message) {
    const container = document.getElementById('groupPostsContainer');
    container.innerHTML = `
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        ${message}
    </div>
    `;
}

function loadGroupMembers(groupId) {
    const container = document.getElementById('groupMembersList');
    
    fetch(`actions/get_group_members.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
            if(data.success && data.members && data.members.length > 0) {
                let html = '';
                data.members.forEach(member => {
                    const badgeClass = member.role === 'admin' ? 'bg-danger' : 
                                     (member.role === 'moderator' ? 'bg-warning' : 'bg-light text-dark');
                    html += `<span class="badge ${badgeClass} mb-1 me-1">${member.username}</span>`;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<span class="badge bg-light">Belum ada anggota</span>';
            }
        })
        .catch(error => {
            console.error('Error loading members:', error);
            container.innerHTML = '<span class="badge bg-light">Gagal memuat</span>';
        });
}

// Handle form submission dengan AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('groupPostForm');
    
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            submitPostForm();
        });
    }
});

function submitPostForm() {
    const form = document.getElementById('groupPostForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitPostBtn');
    const content = document.getElementById('postContent').value.trim();
    
    // Validasi client-side
    if(!content) {
        showAlert('danger', 'Konten tidak boleh kosong');
        return;
    }
    
    if(!currentGroupId) {
        showAlert('danger', 'Group ID tidak valid');
        return;
    }
    
    console.log("Submitting post to group:", currentGroupId);
    
    // Disable button saat proses
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    
    // Kirim request
    fetch('actions/create_group_post_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log("Response status:", response.status);
        
        if(!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log("Response data:", data);
        
        if(data.success) {
            // Reset form
            document.getElementById('postContent').value = '';
            
            // Tampilkan notifikasi sukses
            showAlert('success', data.message);
            
            // Reload postingan setelah 1 detik
            setTimeout(() => {
                loadGroupPosts(currentGroupId);
            }, 1000);
            
        } else {
            showAlert('danger', data.message || 'Gagal membuat postingan');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        showAlert('danger', 'Terjadi kesalahan jaringan. Periksa koneksi internet Anda.');
    })
    .finally(() => {
        // Enable button kembali
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function showAlert(type, message) {
    // Hapus alert sebelumnya
    const existingAlerts = document.querySelectorAll('.alert-temporary');
    existingAlerts.forEach(alert => {
        if(alert.parentNode) alert.parentNode.removeChild(alert);
    });
    
    // Buat elemen alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-temporary alert-dismissible fade show`;
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            <div>${message}</div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Tambahkan ke body
    document.body.appendChild(alertDiv);
    
    // Hapus otomatis setelah 5 detik
    setTimeout(() => {
        if(alertDiv.parentNode) {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }
    }, 5000);
}

// Handle modal events
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('groupDetailModal');
    if(modal) {
        modal.addEventListener('shown.bs.modal', function() {
            document.getElementById('postContent').focus();
        });
    }
});
</script>

<?php include 'footer.php'; ?>