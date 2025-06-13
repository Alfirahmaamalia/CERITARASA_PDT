<?php
require_once __DIR__ . '/../core/Database.php';

class ResepDicoba {
    private $db;
    protected $table = 'resep_dicoba';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // [UPDATED]: Menggunakan stored procedure toggle_resep_dicoba dengan transaction
    public function markAsTried($user_id, $resep_id) {
        try {
            $query = "CALL toggle_resep_dicoba(:user_id, :resep_id, @action)";
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
            error_log("Error in markAsTried: " . $e->getMessage());
            return false;
        }
    }

    // [UPDATED]: Menggunakan stored procedure toggle_resep_dicoba dengan transaction
    public function unmarkAsTried($user_id, $resep_id) {
        try {
            $query = "CALL toggle_resep_dicoba(:user_id, :resep_id, @action)";
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
            error_log("Error in unmarkAsTried: " . $e->getMessage());
            return false;
        }
    }

    // [UPDATED]: Menggunakan stored procedure untuk toggle (mark/unmark)
    public function toggleTried($user_id, $resep_id) {
        try {
            $query = "CALL toggle_resep_dicoba(:user_id, :resep_id, @action)";
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
            error_log("Error in toggleTried: " . $e->getMessage());
            return false;
        }
    }
}
?>
