<?php
class Post {
    private $conn;
    private $table_name = "posts";

    public $id;
    public $user_id;
    public $content;
    public $image;
    public $post_type;
    public $created_at;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public static function create($conn, $user_id, $content, $image = null, $post_type = 'text') {
        $query = "INSERT INTO posts SET user_id=:user_id, content=:content, image=:image, post_type=:post_type";
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":post_type", $post_type);
        
        return $stmt->execute();
    }

    public static function getAll($conn, $limit = 10, $offset = 0) {
        $query = "SELECT p.*, u.username, u.full_name, u.profile_picture, 
                         COUNT(DISTINCT l.id) as likes_count,
                         COUNT(DISTINCT c.id) as comments_count
                  FROM posts p 
                  JOIN users u ON p.user_id = u.id 
                  LEFT JOIN likes l ON p.id = l.post_id 
                  LEFT JOIN comments c ON p.id = c.post_id 
                  WHERE u.is_banned = 0 
                  GROUP BY p.id 
                  ORDER BY p.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($conn, $id) {
        $query = "SELECT p.*, u.username, u.full_name, u.profile_picture 
                  FROM posts p 
                  JOIN users u ON p.user_id = u.id 
                  WHERE p.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public static function like($conn, $user_id, $post_id) {
        // Check if already liked
        $check_query = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(1, $user_id);
        $check_stmt->bindParam(2, $post_id);
        $check_stmt->execute();
        
        if($check_stmt->rowCount() > 0) {
            // Unlike
            $query = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        } else {
            // Like
            $query = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        }
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $post_id);
        return $stmt->execute();
    }

    public static function isLiked($conn, $user_id, $post_id) {
        $query = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $post_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>