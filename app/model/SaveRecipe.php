<?php
require_once __DIR__ . '/../core/Database.php';

class SavedRecipe {
    private $db;
    protected $table = 'saved_recipes';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // [UPDATED]: Menggunakan stored procedure toggle_saved_recipe dengan transaction
    public function saveRecipe($user_id, $resep_id) {
        try {
            $query = "CALL toggle_saved_recipe(:user_id, :resep_id, @action)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':resep_id', $resep_id);
            $result = $stmt->execute();
            
            if ($result) {
                // Get the action result
                $stmt = $this->db->query("SELECT @action as action");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['action'] === 'added';
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in saveRecipe: " . $e->getMessage());
            return false;
        }
    }

    // [UPDATED]: Menggunakan stored procedure toggle_saved_recipe dengan transaction
    public function unsaveRecipe($user_id, $resep_id) {
        try {
            $query = "CALL toggle_saved_recipe(:user_id, :resep_id, @action)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':resep_id', $resep_id);
            $result = $stmt->execute();
            
            if ($result) {
                // Get the action result
                $stmt = $this->db->query("SELECT @action as action");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['action'] === 'removed';
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in unsaveRecipe: " . $e->getMessage());
            return false;
        }
    }

    // [UPDATED]: Menggunakan stored function get_total_tried
    public function getSavedRecipes($user_id) {
        $query = "SELECT r.*, u.username, 
                  get_total_tried(r.id) as total_tried 
                  FROM saved_recipes sr 
                  JOIN resep r ON sr.resep_id = r.id 
                  JOIN users u ON r.user_id = u.id 
                  WHERE sr.user_id = :user_id 
                  ORDER BY sr.saved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isSaved($user_id, $resep_id) {
        $query = "SELECT * FROM saved_recipes WHERE user_id = :user_id AND resep_id = :resep_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':resep_id', $resep_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // [UPDATED]: Menggunakan stored procedure untuk toggle save/unsave
    public function toggleSave($user_id, $resep_id) {
        try {
            $query = "CALL toggle_saved_recipe(:user_id, :resep_id, @action)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':resep_id', $resep_id);
            $result = $stmt->execute();
            
            if ($result) {
                // Get the action result
                $stmt = $this->db->query("SELECT @action as action");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['action'];
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error in toggleSave: " . $e->getMessage());
            return false;
        }
    }
}
?>
