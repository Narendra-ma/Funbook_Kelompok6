<?php
include 'header.php';

// Cek apakah admin
if(!$_SESSION['is_admin']){
    header("Location: index.php");
    exit;
}

// Ambil statistik
$sql = "SELECT COUNT(*) as total FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total_users = $stmt->fetch()['total'];

$sql = "SELECT COUNT(*) as total FROM posts";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total_posts = $stmt->fetch()['total'];

// PERBAIKAN: Tambah backticks untuk tabel groups
$sql = "SELECT COUNT(*) as total FROM `groups`";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total_groups = $stmt->fetch()['total'];

// Ambil semua users
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$all_users = $stmt->fetchAll();

// Ambil semua posts
$sql = "SELECT posts.*, users.username, users.full_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$all_posts = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-cog me-2"></i>Admin Panel
                </h4>
            </div>
            <div class="card-body">
                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h4><?php echo $total_users; ?></h4>
                                <p class="mb-0">Total Pengguna</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h4><?php echo $total_posts; ?></h4>
                                <p class="mb-0">Total Postingan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h4><?php echo $total_groups; ?></h4>
                                <p class="mb-0">Total Grup</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manajemen User -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Manajemen Pengguna</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($all_users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo $user['full_name']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td>
                                            <?php if($user['is_admin']): ?>
                                            <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user['is_banned']): ?>
                                            <span class="badge bg-danger">Banned</span>
                                            <?php else: ?>
                                            <span class="badge bg-success">Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                    <?php if(!$user['is_admin']): ?>
                                                    <!-- Tombol Make Admin -->
                                                    <form method="POST" action="actions/make_admin.php" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-info btn-sm" 
                                                                onclick="return confirm('Apakah Anda yakin ingin menjadikan <?php echo htmlspecialchars($user['full_name']); ?> sebagai admin?')">
                                                            Jadikan Admin
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                    <!-- Tombol Remove Admin -->
                                                    <form method="POST" action="actions/remove_admin.php" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus hak admin dari <?php echo htmlspecialchars($user['full_name']); ?>?')">
                                                            Hapus Admin
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    
                                                    <?php if(!$user['is_banned']): ?>
                                                    <!-- Tombol Ban -->
                                                    <form method="POST" action="actions/ban_user.php" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm">Ban</button>
                                                    </form>
                                                    <?php else: ?>
                                                    <!-- Tombol Unban -->
                                                    <form method="POST" action="actions/unban_user.php" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-success btn-sm">Unban</button>
                                                    </form>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Anda</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Manajemen Post -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Manajemen Postingan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Konten</th>
                                        <th>User</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($all_posts as $post): ?>
                                    <tr>
                                        <td><?php echo $post['id']; ?></td>
                                        <td>
                                            <?php 
                                            $content = htmlspecialchars($post['content']);
                                            echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                            ?>
                                        </td>
                                        <td><?php echo $post['full_name']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" action="actions/delete_post_admin.php" class="d-inline">
                                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>