<?php
include 'header.php';

// Ambil semua reels
$sql = "SELECT reels.*, users.username, users.profile_picture 
        FROM reels 
        JOIN users ON reels.user_id = users.id 
        ORDER BY reels.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reels = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-6 mx-auto">

        <!-- Upload button -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReelModal">
                    <i class="fas fa-upload me-1"></i>Upload Reel
                </button>
            </div>
        </div>

        <!-- List Reels -->
        <?php foreach($reels as $reel): ?>
        <div class="card mb-4">
            <div class="card-body">

                <div class="d-flex align-items-center mb-3">
                    <img src="assets/images/profiles/<?php echo $reel['profile_picture']; ?>" 
                         class="rounded-circle" width="50" height="50">
                    <strong class="ms-3">@<?php echo $reel['username']; ?></strong>
                </div>

                <video controls class="w-100 mb-3" style="max-height: 500px;">
                    <source src="assets/reels/<?php echo $reel['video']; ?>" type="video/mp4">
                </video>

                <p><?php echo htmlspecialchars($reel['caption']); ?></p>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-outline-primary btn-sm like-btn" data-id="<?php echo $reel['id']; ?>" data-type="like">
                        <i class="far fa-thumbs-up"></i> Like
                    </button>
                    <button class="btn btn-outline-danger btn-sm like-btn" data-id="<?php echo $reel['id']; ?>" data-type="dislike">
                        <i class="far fa-thumbs-down"></i> Dislike
                    </button>
                </div>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php include 'footer.php'; ?>
