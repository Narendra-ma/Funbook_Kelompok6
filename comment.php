<?php
include 'header.php';

// Cek parameter post_id
if(!isset($_GET['post_id'])){
    header("Location: index.php");
    exit;
}

$post_id = $_GET['post_id'];

// Ambil data postingan
$sql = "SELECT posts.*, users.username, users.full_name, users.profile_picture, users.id as user_id 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if(!$post){
    header("Location: index.php");
    exit;
}

// Ambil komentar untuk postingan ini
$sql = "SELECT comments.*, users.username, users.full_name, users.profile_picture, users.id as user_id 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <!-- Postingan -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                             class="rounded-circle me-3" width="45" height="45">
                        <div>
                            <!-- Nama user jadi link ke profile -->
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark">
                                <h6 class="fw-bold mb-0"><?php echo $post['full_name']; ?></h6>
                            </a>
                            <small class="text-muted">
                                <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <p class="mb-3"><?php echo htmlspecialchars($post['content']); ?></p>
                
                <?php if($post['image']): ?>
                <img src="assets/images/posts/<?php echo $post['image']; ?>" 
                     class="img-fluid rounded mb-3" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>

                <div class="d-flex justify-content-between border-top pt-3">
                    <button class="btn btn-sm btn-outline-primary like-btn" data-post-id="<?php echo $post['id']; ?>">
                        <i class="far fa-thumbs-up"></i> Suka
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="far fa-comment"></i> Komentar (<?php echo count($comments); ?>)
                    </button>
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="far fa-share-square"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>

        <!-- Komentar -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Komentar (<?php echo count($comments); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if(count($comments) > 0): ?>
                    <?php foreach($comments as $comment): ?>
                    <div class="d-flex mb-3">
                        <img src="assets/images/profiles/<?php echo $comment['profile_picture']; ?>" 
                             class="rounded-circle me-3" width="45" height="45">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <!-- Nama komentator jadi link ke profile -->
                                    <a href="profile.php?id=<?php echo $comment['user_id']; ?>" 
                                       class="text-decoration-none text-dark fw-bold">
                                        <?php echo $comment['full_name']; ?>
                                    </a>
                                    <small class="text-muted d-block">
                                        <?php echo date('d M Y H:i', strtotime($comment['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                            <p class="mb-0 mt-1"><?php echo htmlspecialchars($comment['content']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada komentar</h5>
                        <p class="text-muted">Jadilah yang pertama berkomentar!</p>
                    </div>
                <?php endif; ?>
                
                <!-- Form tambah komentar -->
                <form method="POST" action="actions/add_comment.php" class="mt-3">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="content" placeholder="Tulis komentar..." required>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>