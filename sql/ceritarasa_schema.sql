-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- resep
CREATE TABLE resep (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  bahan TEXT,
  langkah TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- resep_dicoba
CREATE TABLE resep_dicoba (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  resep_id INT NOT NULL,
  tried_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resep_id) REFERENCES resep(id) ON DELETE CASCADE
);

-- Function hitung jumlah coba
DELIMITER $$
CREATE FUNCTION get_total_tried(p_resep_id INT)
RETURNS INT
DETERMINISTIC
BEGIN
  DECLARE v INT;
  SELECT COUNT(*) INTO v FROM resep_dicoba WHERE resep_id = p_resep_id;
  RETURN IFNULL(v,0);
END $$
DELIMITER ;

-- Trigger: log create resep
CREATE TABLE log_aktivitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  aktivitas VARCHAR(255),
  waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

DELIMITER $$
CREATE TRIGGER trg_resep_after_insert
AFTER INSERT ON resep
FOR EACH ROW
BEGIN
  INSERT INTO log_aktivitas(user_id, aktivitas)
  VALUES(NEW.user_id, CONCAT('Menambahkan resep: ', NEW.judul));
END $$
DELIMITER ;

-- Procedure: tambah resep menggunakan stored procedure
DELIMITER $$
CREATE PROCEDURE add_resep(
  IN p_user INT,
  IN p_judul VARCHAR(255),
  IN p_deskripsi TEXT,
  IN p_bahan TEXT,
  IN p_langkah TEXT
)
BEGIN
  INSERT INTO resep(user_id, judul, deskripsi, bahan, langkah)
  VALUES(p_user, p_judul, p_deskripsi, p_bahan, p_langkah);
END $$
DELIMITER ;
