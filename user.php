<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $bio;
    public $profile_picture;
    public $cover_photo;
    public $is_admin;
    public $is_banned;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public static function find($conn, $username, $password) {
        $query = "SELECT * FROM users WHERE (username = :username OR email = :username) AND is_banned = 0";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $row['password'])) {
                $user = new User($conn);
                $user->id = $row['id'];
                $user->username = $row['username'];
                $user->email = $row['email'];
                $user->full_name = $row['full_name'];
                $user->bio = $row['bio'];
                $user->profile_picture = $row['profile_picture'];
                $user->cover_photo = $row['cover_photo'];
                $user->is_admin = $row['is_admin'];
                $user->is_banned = $row['is_banned'];
                return $user;
            }
        }
        return false;
    }

    public static function register($conn, $username, $email, $password, $full_name) {
        $query = "INSERT INTO users SET username=:username, email=:email, password=:password, full_name=:full_name";
        $stmt = $conn->prepare($query);
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":full_name", $full_name);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public static function getById($conn, $id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function updateProfile($bio, $profile_picture, $cover_photo) {
        $query = "UPDATE users SET bio=:bio, profile_picture=:profile_picture, cover_photo=:cover_photo WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":profile_picture", $profile_picture);
        $stmt->bindParam(":cover_photo", $cover_photo);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }
    // Tambahkan method ini di class User yang sudah ada

public static function getTotalUsers($conn) {
    $query = "SELECT COUNT(*) as total FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public static function getAllUsers($conn) {
    $query = "SELECT id, username, email, full_name, is_banned, created_at 
              FROM users 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>