-- [UPDATED]: Schema dengan stored procedures dan functions yang diperlukan

-- Drop existing procedures and functions if they exist
DROP PROCEDURE IF EXISTS add_resep;
DROP FUNCTION IF EXISTS get_total_tried;
DROP FUNCTION IF EXISTS get_saved_count;

-- Stored Procedure: Menambahkan resep baru
DELIMITER $$
CREATE PROCEDURE add_resep(
  IN p_user_id INT,
  IN p_judul VARCHAR(255),
  IN p_deskripsi TEXT,
  IN p_bahan TEXT,
  IN p_langkah TEXT,
  IN p_cuisine_type VARCHAR(50),
  IN p_difficulty_level VARCHAR(10),
  IN p_cooking_time INT,
  IN p_servings INT,
  IN p_image_url VARCHAR(255),
  OUT p_resep_id INT
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  
  START TRANSACTION;
  
  -- Insert resep
  INSERT INTO resep(user_id, judul, deskripsi, bahan, langkah, cuisine_type, difficulty_level, cooking_time, servings, image_url)
  VALUES(p_user_id, p_judul, p_deskripsi, p_bahan, p_langkah, p_cuisine_type, p_difficulty_level, p_cooking_time, p_servings, p_image_url);
  
  -- Get the inserted ID
  SET p_resep_id = LAST_INSERT_ID();
  
  -- Log aktivitas
  INSERT INTO log_aktivitas(user_id, aktivitas)
  VALUES(p_user_id, CONCAT('Menambahkan resep: ', p_judul));
  
  COMMIT;
END $$
DELIMITER ;

-- Stored Function: Menghitung jumlah pengguna yang mencoba resep
DELIMITER $$
CREATE FUNCTION get_total_tried(p_resep_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count FROM resep_dicoba WHERE resep_id = p_resep_id;
  RETURN IFNULL(v_count, 0);
END $$
DELIMITER ;

-- Stored Function: Menghitung jumlah resep yang disimpan user
DELIMITER $$
CREATE FUNCTION get_saved_count(p_user_id INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
  DECLARE v_count INT DEFAULT 0;
  SELECT COUNT(*) INTO v_count FROM saved_recipes WHERE user_id = p_user_id;
  RETURN IFNULL(v_count, 0);
END $$
DELIMITER ;

-- Stored Procedure: Toggle resep dicoba dengan transaction
DELIMITER $$
CREATE PROCEDURE toggle_resep_dicoba(
  IN p_user_id INT,
  IN p_resep_id INT,
  OUT p_action VARCHAR(20)
)
BEGIN
  DECLARE v_exists INT DEFAULT 0;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  
  START TRANSACTION;
  
  -- Check if already tried
  SELECT COUNT(*) INTO v_exists FROM resep_dicoba WHERE user_id = p_user_id AND resep_id = p_resep_id;
  
  IF v_exists > 0 THEN
    -- Remove from tried
    DELETE FROM resep_dicoba WHERE user_id = p_user_id AND resep_id = p_resep_id;
    SET p_action = 'removed';
  ELSE
    -- Add to tried
    INSERT INTO resep_dicoba(user_id, resep_id) VALUES(p_user_id, p_resep_id);
    SET p_action = 'added';
  END IF;
  
  COMMIT;
END $$
DELIMITER ;

-- Stored Procedure: Toggle saved recipe dengan transaction
DELIMITER $$
CREATE PROCEDURE toggle_saved_recipe(
  IN p_user_id INT,
  IN p_resep_id INT,
  OUT p_action VARCHAR(20)
)
BEGIN
  DECLARE v_exists INT DEFAULT 0;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;
  
  START TRANSACTION;
  
  -- Check if already saved
  SELECT COUNT(*) INTO v_exists FROM saved_recipes WHERE user_id = p_user_id AND resep_id = p_resep_id;
  
  IF v_exists > 0 THEN
    -- Remove from saved
    DELETE FROM saved_recipes WHERE user_id = p_user_id AND resep_id = p_resep_id;
    SET p_action = 'removed';
  ELSE
    -- Add to saved
    INSERT IGNORE INTO saved_recipes(user_id, resep_id) VALUES(p_user_id, p_resep_id);
    SET p_action = 'added';
  END IF;
  
  COMMIT;
END $$
DELIMITER ;
