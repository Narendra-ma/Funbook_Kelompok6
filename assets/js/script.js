// Fungsi like post dengan AJAX
$(document).ready(function() {
    // Like functionality
    $(document).on('click', '.like-btn', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        var icon = btn.find('i');
        
        $.ajax({
            url: 'actions/like_post.php',
            type: 'POST',
            data: { post_id: postId },
            success: function(response) {
                if(response === 'liked') {
                    // Ubah icon menjadi solid dan beri warna biru
                    icon.removeClass('far').addClass('fas text-primary');
                    btn.addClass('text-primary');
                } else if(response === 'unliked') {
                    // Kembalikan icon ke regular dan hapus warna
                    icon.removeClass('fas text-primary').addClass('far');
                    btn.removeClass('text-primary');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menyukai postingan');
            }
        });
    });

    // Auto close alert setelah 5 detik
    $('.alert').delay(5000).fadeOut(300);

    // Form validation sederhana
    $('form').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    });

    // Smooth scroll untuk anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if(target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });

    // Preview image sebelum upload
    $('input[type="file"]').on('change', function() {
        var file = this.files[0];
        if(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Tampilkan preview jika ada elemen preview
                var preview = $(this).closest('form').find('.image-preview');
                if(preview.length) {
                    preview.html('<img src="' + e.target.result + '" class="img-thumbnail mt-2" style="max-height: 200px;">');
                }
            }.bind(this);
            reader.readAsDataURL(file);
        }
    });

    // Auto-hide modal setelah form submit success
    $('form').on('submit', function(e) {
        var form = $(this);
        var modal = form.closest('.modal');
        
        // Jika form dalam modal, tunggu sebentar lalu tutup
        if(modal.length) {
            setTimeout(function() {
                if(!form.find('.alert-danger').length) {
                    modal.modal('hide');
                }
            }, 1000);
        }
    });

    // Tab functionality dengan smooth transition

    // Tooltip initialization
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Auto-focus pada modal input
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input[type="text"], textarea').first().focus();
    });

    // Confirm sebelum hapus
    $('form').on('submit', function(e) {
        var form = $(this);
        var isDeleteAction = form.attr('action').includes('delete') || 
                            form.attr('action').includes('remove') || 
                            form.attr('action').includes('ban');
        
        if(isDeleteAction) {
            if(!confirm('Apakah Anda yakin ingin melakukan tindakan ini?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Infinite scroll untuk posts (sederhana)
    var loading = false;
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadMorePosts();
        }
    });

    function loadMorePosts() {
        if(loading) return;
        
        loading = true;
        var lastPostId = $('.post-card:last').data('post-id');
        
        // Tampilkan loading indicator
        $('#posts-container').append('<div class="text-center py-3" id="loading-posts"><i class="fas fa-spinner fa-spin"></i> Memuat postingan...</div>');
        
        // Simulasi load more (bisa dikembangkan dengan AJAX)
        setTimeout(function() {
            $('#loading-posts').remove();
            loading = false;
        }, 1000);
    }

    // Friend request dengan AJAX
    $(document).on('submit', 'form[action*="add_friend"]', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response === 'success') {
                    btn.html('<i class="fas fa-check"></i> Permintaan Dikirim');
                    btn.removeClass('btn-primary').addClass('btn-secondary');
                } else if(response === 'already_exists') {
                    btn.html('<i class="fas fa-clock"></i> Menunggu Konfirmasi');
                    btn.removeClass('btn-primary').addClass('btn-secondary');
                } else {
                    alert('Gagal mengirim permintaan pertemanan');
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Accept friend request dengan AJAX
    $(document).on('submit', 'form[action*="accept_friend"]', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var card = form.closest('.card');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response === 'success') {
                    card.fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Gagal menerima permintaan pertemanan');
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    });

    // Responsive menu untuk mobile
    function checkScreenSize() {
        if($(window).width() < 768) {
            $('.navbar-nav').addClass('flex-row');
        } else {
            $('.navbar-nav').removeClass('flex-row');
        }
    }

    $(window).resize(checkScreenSize);
    checkScreenSize();

    // Dark mode toggle (sederhana)
    $('#dark-mode-toggle').on('click', function() {
        $('body').toggleClass('dark-mode');
        localStorage.setItem('darkMode', $('body').hasClass('dark-mode'));
    });

    // Load dark mode preference
    if(localStorage.getItem('darkMode') === 'true') {
        $('body').addClass('dark-mode');
    }

    // Image lazy loading
    $('img').each(function() {
        var img = $(this);
        if(!img.attr('src') && img.attr('data-src')) {
            img.attr('src', img.attr('data-src'));
        }
    });

    // Password strength indicator
    $('input[type="password"]').on('keyup', function() {
        var password = $(this).val();
        var strength = 0;
        
        if(password.length >= 6) strength++;
        if(password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if(password.match(/\d/)) strength++;
        if(password.match(/[^a-zA-Z\d]/)) strength++;
        
        var strengthText = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
        var strengthClass = ['danger', 'warning', 'info', 'primary', 'success'];
        
        $('#password-strength').text(strengthText[strength]).removeClass().addClass('text-' + strengthClass[strength]);
    });

    // Auto-logout setelah 30 menit inactivity
    var idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 menit
    
    $(document).mousemove(function(e) {
        idleTime = 0;
    });
    
    $(document).keypress(function(e) {
        idleTime = 0;
    });
    
    function timerIncrement() {
        idleTime++;
        if(idleTime > 29) { // 30 menit
            window.location.href = 'logout.php';
        }
    }

    $(document).ready(function() {
        // Handle post likes
        $('.like-btn').click(function(e) {
            e.preventDefault();
            const postId = $(this).data('post-id');
            const button = $(this);
            
            $.post('actions/like_post.php', { post_id: postId }, function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    button.html('<i class="fas fa-thumbs-up"></i> ' + data.likes + ' Suka');
                    button.removeClass('btn-outline-primary').addClass('btn-primary');
                }
            });
        });
        
        // Handle reel likes
        $('.reel-like-btn').click(function(e) {
            e.preventDefault();
            const reelId = $(this).data('reel-id');
            const type = $(this).data('type');
            const button = $(this);
            
            $.post('actions/reel_like.php', { reel_id: reelId, type: type }, function(response) {
                button.html('<i class="fas fa-thumbs-up"></i> Liked');
                button.removeClass('btn-outline-primary').addClass('btn-primary');
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Video play on hover for reels
        $('.reel-video').hover(function() {
            if($(this).prop('paused')) {
                $(this).trigger('play');
            }
        }, function() {
            $(this).trigger('pause');
        });
        
        // Image modal for memes
        $('.meme-image').click(function() {
            const imgSrc = $(this).attr('src');
            $('#memeModalImage').attr('src', imgSrc);
            $('#memeModal').modal('show');
        });
        
        // Infinite scroll for posts
        let loading = false;
        let page = 1;
        
        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                if(!loading) {
                    loading = true;
                    page++;
                    loadMorePosts(page);
                }
            }
        });
        
        function loadMorePosts(page) {
            $.get('actions/load_posts.php', { page: page }, function(response) {
                $('#posts-container').append(response);
                loading = false;
            });
        }
    });
    
    // Function to share content
    function shareContent(type, id) {
        const url = window.location.href;
        const shareText = `Lihat ini di FunBook: ${url}`;
        
        if(navigator.share) {
            navigator.share({
                title: 'FunBook',
                text: shareText,
                url: url
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(shareText).then(function() {
                alert('Link berhasil disalin!');
            });
        }
    }
    
    // Function to follow user
    function followUser(userId, button) {
        $.post('actions/follow.php', { user_id: userId }, function(response) {
            const data = JSON.parse(response);
            if(data.success) {
                button.innerHTML = '<i class="fas fa-check"></i> Mengikuti';
                button.classList.remove('btn-primary');
                button.classList.add('btn-secondary');
                button.disabled = true;
            }
        });
    }
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
    console.log('Funbook JavaScript loaded successfully!');
});