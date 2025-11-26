<?php
include 'header.php';

// Check if user is admin
if(!$_SESSION['is_admin']){
    header("Location: index.php");
    exit;
}

include 'class/User.php';
include 'class/Post.php';

// Get statistics
$total_users = User::getTotalUsers($conn);
$total_posts = Post::getTotalPosts($conn);
$reported_posts = Post::getReportedPosts($conn);
?>

<div class="row">
    <div class="col-12">
        <h4><i class="fas fa-cog me-2"></i>Admin Panel</h4>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $total_users; ?></h4>
                                <p class="mb-0">Total Pengguna</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $total_posts; ?></h4>
                                <p class="mb-0">Total Postingan</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo count($reported_posts); ?></h4>
                                <p class="mb-0">Postingan Dilaporkan</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-flag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management -->
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
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $users = User::getAllUsers($conn);
                            foreach($users as $user): 
                            ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if($user['is_banned']): ?>
                                        <span class="badge bg-danger">Banned</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!$user['is_banned']): ?>
                                        <button class="btn btn-sm btn-warning ban-user" data-user-id="<?php echo $user['id']; ?>">
                                            Ban
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-success unban-user" data-user-id="<?php echo $user['id']; ?>">
                                            Unban
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Post Management -->
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
                                <th>Pembuat</th>
                                <th>Tanggal</th>
                                <th>Likes</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $all_posts = Post::getAllPostsForAdmin($conn);
                            foreach($all_posts as $post): 
                            ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td>
                                <td>
                                    <?php 
                                    $content = htmlspecialchars($post['content']);
                                    echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($post['full_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($post['created_at'])); ?></td>
                                <td><?php echo $post['likes_count']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-post-admin" data-post-id="<?php echo $post['id']; ?>">
                                        Hapus
                                    </button>
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

<?php include 'footer.php'; ?>