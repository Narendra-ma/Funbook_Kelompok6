<?php
class Friend {
    private $conn;
    private $table_name = "friends";

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public static function sendRequest($conn, $user_id1, $user_id2) {
        // Check if request already exists
        $check_query = "SELECT id FROM friends WHERE 
                       (user_id1 = ? AND user_id2 = ?) OR 
                       (user_id1 = ? AND user_id2 = ?)";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(1, $user_id1);
        $check_stmt->bindParam(2, $user_id2);
        $check_stmt->bindParam(3, $user_id2);
        $check_stmt->bindParam(4, $user_id1);
        $check_stmt->execute();

        if($check_stmt->rowCount() > 0) {
            return false; // Request already exists
        }

        $query = "INSERT INTO friends (user_id1, user_id2, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id1);
        $stmt->bindParam(2, $user_id2);
        
        return $stmt->execute();
    }

    public static function acceptRequest($conn, $user_id1, $user_id2) {
        $query = "UPDATE friends SET status = 'accepted' 
                  WHERE user_id1 = ? AND user_id2 = ? AND status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id1);
        $stmt->bindParam(2, $user_id2);
        
        return $stmt->execute();
    }

    public static function getFriends($conn, $user_id) {
        $query = "SELECT u.id, u.username, u.full_name, u.profile_picture 
                  FROM users u 
                  INNER JOIN friends f ON 
                  (f.user_id1 = u.id OR f.user_id2 = u.id) 
                  WHERE (f.user_id1 = ? OR f.user_id2 = ?) 
                  AND f.status = 'accepted' 
                  AND u.id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $user_id);
        $stmt->bindParam(3, $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPendingRequests($conn, $user_id) {
        $query = "SELECT u.id, u.username, u.full_name, u.profile_picture 
                  FROM users u 
                  INNER JOIN friends f ON f.user_id1 = u.id 
                  WHERE f.user_id2 = ? AND f.status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function isFriend($conn, $user_id1, $user_id2) {
        $query = "SELECT id FROM friends 
                  WHERE ((user_id1 = ? AND user_id2 = ?) OR 
                         (user_id1 = ? AND user_id2 = ?)) 
                  AND status = 'accepted'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id1);
        $stmt->bindParam(2, $user_id2);
        $stmt->bindParam(3, $user_id2);
        $stmt->bindParam(4, $user_id1);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public static function removeFriend($conn, $user_id1, $user_id2) {
        $query = "DELETE FROM friends 
                  WHERE (user_id1 = ? AND user_id2 = ?) OR 
                        (user_id1 = ? AND user_id2 = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $user_id1);
        $stmt->bindParam(2, $user_id2);
        $stmt->bindParam(3, $user_id2);
        $stmt->bindParam(4, $user_id1);
        
        return $stmt->execute();
    }
}
?>