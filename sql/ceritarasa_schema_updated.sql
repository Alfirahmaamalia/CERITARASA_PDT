-- Update existing tables and add new fields
ALTER TABLE resep ADD COLUMN cuisine_type VARCHAR(50) DEFAULT 'Indonesian';
ALTER TABLE resep ADD COLUMN difficulty_level ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Easy';
ALTER TABLE resep ADD COLUMN cooking_time INT DEFAULT 30; -- in minutes
ALTER TABLE resep ADD COLUMN servings INT DEFAULT 4;
ALTER TABLE resep ADD COLUMN image_url VARCHAR(255) DEFAULT NULL;

-- Create saved recipes table
CREATE TABLE saved_recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  resep_id INT NOT NULL,
  saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE,
  UNIQUE KEY unique_save (user_id, resep_id)
);

-- Update stored procedure for adding recipe
DROP PROCEDURE IF EXISTS add_resep;

DELIMITER $$
CREATE PROCEDURE add_resep(
  IN p_user INT,
  IN p_judul VARCHAR(255),
  IN p_deskripsi TEXT,
  IN p_bahan TEXT,
  IN p_langkah TEXT,
  IN p_cuisine_type VARCHAR(50),
  IN p_difficulty_level VARCHAR(10),
  IN p_cooking_time INT,
  IN p_servings INT,
  IN p_image_url VARCHAR(255)
)
BEGIN
  INSERT INTO resep(user_id, judul, deskripsi, bahan, langkah, cuisine_type, difficulty_level, cooking_time, servings, image_url)
  VALUES(p_user, p_judul, p_deskripsi, p_bahan, p_langkah, p_cuisine_type, p_difficulty_level, p_cooking_time, p_servings, p_image_url);
END $$
DELIMITER ;

-- Function to get saved recipes count
DELIMITER $$
CREATE FUNCTION get_saved_count(p_user_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
  DECLARE v INT;
  SELECT COUNT(*) INTO v FROM saved_recipes WHERE user_id = p_user_id;
  RETURN IFNULL(v,0);
END $$
DELIMITER ;
