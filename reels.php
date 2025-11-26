<?php
include 'header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-play-circle me-2"></i>Reels</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReelModal">
                <i class="fas fa-upload me-1"></i>Upload Reel
            </button>
        </div>
        
        <div class="row" id="reels-container">
            <!-- Sample Reels -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="reel-card card">
                    <div class="reel-video position-relative">
                        <img src="https://via.placeholder.com/300x500/007bff/ffffff?text=Reel+1" 
                             class="card-img-top" style="height: 400px; object-fit: cover;">
                        <div class="reel-overlay position-absolute bottom-0 start-0 end-0 p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=User+1&background=28a745&color=fff" 
                                         class="profile-img-sm me-2">
                                    <span>User 1</span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-light me-1">
                                        <i class="far fa-heart"></i> 125
                                    </button>
                                    <button class="btn btn-sm btn-outline-light">
                                        <i class="far fa-comment"></i> 23
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="reel-card card">
                    <div class="reel-video position-relative">
                        <img src="https://via.placeholder.com/300x500/dc3545/ffffff?text=Reel+2" 
                             class="card-img-top" style="height: 400px; object-fit: cover;">
                        <div class="reel-overlay position-absolute bottom-0 start-0 end-0 p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=User+2&background=dc3545&color=fff" 
                                         class="profile-img-sm me-2">
                                    <span>User 2</span>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-light me-1">
                                        <i class="far fa-heart"></i> 89
                                    </button>
                                    <button class="btn btn-sm btn-outline-light">
                                        <i class="far fa-comment"></i> 15
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Reel Modal -->
<div class="modal fade" id="uploadReelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Reel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadReelForm">
                    <div class="mb-3">
                        <label class="form-label">Pilih Video</label>
                        <input type="file" class="form-control" name="reel_video" accept="video/*" required>
                        <div class="form-text">Format yang didukung: MP4, MOV, AVI. Maksimal 50MB.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Tambahkan deskripsi..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Upload Reel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>