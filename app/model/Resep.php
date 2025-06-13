<?php
require_once __DIR__ . '/../core/Database.php';

class Resep {
    private $db;
    protected $table = 'resep';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // [UPDATED]: Menggunakan stored function get_total_tried untuk menghitung jumlah tried
    public function getAllWithUser() {
        $query = "SELECT r.*, u.username, 
                  get_total_tried(r.id) as total_tried 
                  FROM resep r 
                  JOIN users u ON r.user_id = u.id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [UPDATED]: Menggunakan stored function get_total_tried
    public function getByUserId($user_id) {
        $query = "SELECT r.*, 
                  get_total_tried(r.id) as total_tried 
                  FROM resep r 
                  WHERE r.user_id = :user_id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [UPDATED]: Menggunakan stored function get_total_tried
    public function getByIdWithUser($id) {
        $query = "SELECT r.*, u.username, 
                  get_total_tried(r.id) as total_tried 
                  FROM resep r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // [UPDATED]: Menggunakan stored procedure add_resep dengan transaction
    public function addResep($user_id, $judul, $deskripsi, $bahan, $langkah, $cuisine_type = 'Indonesian', $difficulty_level = 'Easy', $cooking_time = 30, $servings = 4, $image_url = null) {
        try {
            // [UPDATED]: Menggunakan stored procedure add_resep
            $query = "CALL add_resep(:user_id, :judul, :deskripsi, :bahan, :langkah, :cuisine_type, :difficulty_level, :cooking_time, :servings, :image_url, @resep_id)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':deskripsi', $deskripsi);
            $stmt->bindParam(':bahan', $bahan);
            $stmt->bindParam(':langkah', $langkah);
            $stmt->bindParam(':cuisine_type', $cuisine_type);
            $stmt->bindParam(':difficulty_level', $difficulty_level);
            $stmt->bindParam(':cooking_time', $cooking_time);
            $stmt->bindParam(':servings', $servings);
            $stmt->bindParam(':image_url', $image_url);
            
            $result = $stmt->execute();
            
            if ($result) {
                // Get the inserted ID from the output parameter
                $stmt = $this->db->query("SELECT @resep_id as resep_id");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['resep_id'];
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in addResep: " . $e->getMessage());
            return false;
        }
    }

    // [UPDATED]: Menggunakan transaction untuk delete resep
    public function deleteRecipe($id) {
        try {
            $this->db->beginTransaction();
            
            // Delete related records first
            $query = "DELETE FROM resep_dicoba WHERE resep_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $query = "DELETE FROM saved_recipes WHERE resep_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            // Delete the recipe
            $query = "DELETE FROM resep WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            
            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error in deleteRecipe: " . $e->getMessage());
            return false;
        }
    }

    public function isTriedByUser($resep_id, $user_id) {
        $query = "SELECT * FROM resep_dicoba WHERE resep_id = :resep_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':resep_id', $resep_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
?>
