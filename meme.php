<?php
include 'header.php';

// Ambil postingan dengan tipe 'meme' dan postingan dengan gambar
$sql = "SELECT posts.*, users.username, users.full_name, users.profile_picture, users.id as user_id
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE (posts.content_type = 'meme' OR posts.image LIKE '%memes/%')
        AND users.is_banned = 0
        ORDER BY posts.created_at DESC 
        LIMIT 30";
$stmt = $conn->prepare($sql);
$stmt->execute();
$meme_posts = $stmt->fetchAll();

// Ambil meme trending (dengan like terbanyak)
$sql_trending = "SELECT p.*, u.username, u.profile_picture, COUNT(pl.id) as like_count
                 FROM posts p
                 JOIN users u ON p.user_id = u.id
                 LEFT JOIN post_likes pl ON p.id = pl.post_id
                 WHERE p.content_type = 'meme' AND u.is_banned = 0
                 GROUP BY p.id
                 ORDER BY like_count DESC
                 LIMIT 10";
$stmt_trending = $conn->prepare($sql_trending);
$stmt_trending->execute();
$trending_memes = $stmt_trending->fetchAll();
?>

<div class="row">
    <!-- Konten Utama Meme -->
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h4 class="mb-0 text-white">
                    <i class="fas fa-gamepad me-2"></i>Meme & Hiburan
                </h4>
            </div>
            <div class="card-body">
                <!-- Upload Button -->
                <div class="text-center mb-4">
                    <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#createMemeModal">
                        <i class="fas fa-plus me-2"></i>Upload Meme Baru
                    </button>
                    <p class="text-muted mt-2 small">
                        Bagikan meme lucu, video hiburan, atau konten menghibur lainnya
                    </p>
                </div>

                <!-- Grid Meme -->
                <?php if(count($meme_posts) > 0): ?>
                <div class="row" id="meme-grid">
                    <?php foreach($meme_posts as $post): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 meme-card">
                            <!-- Meme Image -->
                            <div class="position-relative">
                                <?php if($post['image']): ?>
                                    <?php if(pathinfo($post['image'], PATHINFO_EXTENSION) == 'mp4'): ?>
                                    <!-- Video Meme -->
                                    <video controls class="card-img-top" style="height: 250px; object-fit: cover;">
                                        <source src="assets/<?php echo $post['image']; ?>" type="video/mp4">
                                    </video>
                                    <?php else: ?>
                                    <!-- Image Meme -->
                                    <img src="assets/<?php echo $post['image']; ?>" 
                                         class="card-img-top meme-image" 
                                         style="height: 250px; object-fit: cover; cursor: pointer;">
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <!-- Meme Badge -->
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-warning">Meme</span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- Meme Text -->
                                <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                            </div>
                            
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- User Info -->
                                    <div class="d-flex align-items-center">
                                        <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none">
                                            <img src="assets/images/profiles/<?php echo $post['profile_picture']; ?>" 
                                                 class="rounded-circle me-2" width="30" height="30">
                                        </a>
                                        <div>
                                            <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark small">
                                                <?php echo $post['full_name']; ?>
                                            </a>
                                            <br>
                                            <small class="text-muted"><?php echo date('d M', strtotime($post['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary like-btn" data-post-id="<?php echo $post['id']; ?>">
                                            <i class="far fa-thumbs-up"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="shareContent('meme', <?php echo $post['id']; ?>)">
                                            <i class="far fa-share-square"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Load More Button -->
                <div class="text-center mt-4">
                    <button class="btn btn-outline-warning" id="loadMoreMemes">
                        <i class="fas fa-sync me-1"></i>Muat Lebih Banyak
                    </button>
                </div>
                <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-laugh-beam fa-4x text-warning"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum ada meme</h4>
                    <p class="text-muted mb-4">Jadilah yang pertama membagikan meme lucu di FunBook!</p>
                    <button class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#createMemeModal">
                        <i class="fas fa-plus me-2"></i>Upload Meme Pertama
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Kanan -->
    <div class="col-lg-3">
        <!-- Trending Memes -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Trending Memes</h6>
            </div>
            <div class="card-body">
                <?php if(count($trending_memes) > 0): ?>
                    <?php foreach($trending_memes as $trend): ?>
                    <div class="mb-3 pb-2 border-bottom">
                        <a href="#meme-<?php echo $trend['id']; ?>" class="text-decoration-none">
                            <div class="d-flex align-items-center">
                                <img src="assets/<?php echo $trend['image']; ?>" 
                                     class="rounded me-2" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <small class="fw-bold text-dark d-block">
                                        <?php echo substr($trend['content'], 0, 20) . '...'; ?>
                                    </small>
                                    <small class="text-muted">
                                        @<?php echo $trend['username']; ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">Belum ada meme trending</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meme Categories -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Kategori</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <a href="?category=lucu" class="text-decoration-none small">
                        <i class="fas fa-laugh me-1"></i> Lucu
                    </a>
                </div>
                <div class="mb-2">
                    <a href="?category=gaming" class="text-decoration-none small">
                        <i class="fas fa-gamepad me-1"></i> Gaming
                    </a>
                </div>
                <div class="mb-2">
                    <a href="?category=sehari-hari" class="text-decoration-none small">
                        <i class="fas fa-home me-1"></i> Kehidupan Sehari-hari
                    </a>
                </div>
                <div class="mb-2">
                    <a href="?category=pop-culture" class="text-decoration-none small">
                        <i class="fas fa-music me-1"></i> Pop Culture
                    </a>
                </div>
                <div class="mb-2">
                    <a href="?category=random" class="text-decoration-none small">
                        <i class="fas fa-random me-1"></i> Random
                    </a>
                </div>
            </div>
        </div>

        <!-- Meme Guidelines -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">📝 Panduan Meme</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted">
                    <li class="mb-2">Pastikan meme tidak mengandung SARA</li>
                    <li class="mb-2">Hindari konten dewasa atau vulgar</li>
                    <li class="mb-2">Jangan gunakan konten bajakan</li>
                    <li class="mb-2">Hormati privasi orang lain</li>
                    <li>Gunakan tagar yang relevan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk infinite scroll memes -->
<script>
$(document).ready(function() {
    let memePage = 1;
    let loadingMemes = false;
    
    $('#loadMoreMemes').click(function() {
        if(!loadingMemes) {
            loadingMemes = true;
            memePage++;
            loadMoreMemes(memePage);
        }
    });
    
    function loadMoreMemes(page) {
        $.get('actions/load_memes.php', { page: page }, function(response) {
            if(response.trim() !== '') {
                $('#meme-grid').append(response);
                loadingMemes = false;
            } else {
                $('#loadMoreMemes').html('Tidak ada meme lagi').prop('disabled', true);
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>