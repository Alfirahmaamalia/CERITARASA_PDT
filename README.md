#  🍲 CeritaRasa – Platform Kolaboratif Resep Masakan

CeritaRasa adalah aplikasi berbasis web yang memungkinkan pengguna menyimpan, berbagi, dan memodifikasi resep masakan. Dibangun menggunakan PHP dan MySQL, platform ini menekankan prinsip kolaborasi dan kreatifitas dalam dunia memasak, serta menerapkan fitur-fitur SQL tingkat lanjut seperti transaction, stored procedure, trigger, dan stored function untuk menjaga konsistensi dan integritas data.
![image](https://github.com/user-attachments/assets/1a4e9222-ffd7-472a-b05f-e68eb0222ccc)



---
# 📌 ERD
Berikut adalah ERD dari Sistem CeritaRasa
![image](https://github.com/user-attachments/assets/77fe1655-06b5-4c22-b9c7-8bb8691e122a)

Relasi Utama:
1. users -> recipe: One-to-Many (1 user dapat membuat banyak resep)
2. users -> saved_recipes: One-to-Many (1 user dapat menyimpan banyak resep).
3. users -> recipe_tried: One-to-Many (1 user dapat mencoba banyak resep).
4. recipe -> saved_recipes: One- to-Many (1 resep bisa disimpan oleh banyak user).
5. recipe -> recipe_tried: One-to-Many (1 resep bisa dicoba oleh banyak user).
6. users -> activity_log : One-to-Many (1 user dapat memlikl banyak log aktivitas)
---
# 📌 Detail Konsep
⚠️ Disclaimer:
Konsep implementasi fitur SQL dan peran stored procedure, trigger, transaction, dan stored function dalam proyek ini dirancang khusus untuk kebutuhan CeritaRasa. Struktur dan penerapan dapat berbeda tergantung kebutuhan sistem masing-masing.

# 🧠 Stored Procedure
Stored procedure pada sistem resep ini berfungsi seperti SOP dapur digital yang memastikan setiap langkah penambahan, penyimpanan, maupun interaksi pengguna terhadap resep dilakukan secara konsisten dan aman. Dengan disimpan langsung di lapisan database, semua proses menjadi lebih efisien dan tahan terhadap gangguan aplikasi dari sisi frontend/backend.
![image](https://github.com/user-attachments/assets/f5727592-c64d-447e-9bcb-e61e3eecf1b3)

Beberapa procedure penting yang di gunakan :

**App\Models\Recipe.php**
✅ add_resep(...): Menambahkan resep baru sekaligus mencatat aktivitas pengguna.
```
// Call the add_resep stored procedure
$stmt = $this->conn->prepare("CALL add_resep(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @resep_id)");
$stmt->execute([
    $userId,
    $judul,
    $deskripsi,
    $bahan,
    $langkah,
    $cuisineType,
    $difficulty,
    $cookTime,
    $servings,
    $imageUrl
]);
```

✅ toggle_saved_recipe(...): Menyimpan atau menghapus resep dari daftar favorit pengguna.
```
// Call the toggle_saved_recipe stored procedure
$stmt = $this->conn->prepare("CALL toggle_saved_recipe(?, ?, @action)");
$stmt->execute([$userId, $resepId]);
```

✅ toggle_resep_dicoba(...): Menandai atau membatalkan status bahwa user telah mencoba resep tertentu.
```
// Call the toggle_resep_dicoba stored procedure
$stmt = $this->conn->prepare("CALL toggle_resep_dicoba(?, ?, @action)");
$stmt->execute([$userId, $resepId]);
```
🧩 Dengan menyimpan logika utama ini di sisi database, sistem menjaga integritas data antar user, terutama ketika digunakan secara bersamaan oleh banyak pengguna.

---
# 🚨 Trigger
Trigger pada sistem ini berfungsi sebagai penjaga integritas data yang otomatis aktif saat data baru dimasukkan ke tabel. Seperti halnya alarm keamanan di dapur, trigger ini memastikan aktivitas pengguna selalu dicatat dengan tepat.
**trg_resep_after_insert**
Secara otomatis mencatat ke dalam tabel log setiap kali user menambahkan resep baru:
```
Trigger: log create resep
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
```
Fungsi trigger ini memperkuat jejak audit (audit trail) dan meningkatkan keamanan serta akuntabilitas sistem.

---
# 🔄 Transaction (Transaksi)
Seperti halnya resep masakan yang gagal jika satu langkah terlewat, sistem ini juga menggunakan konsep all-or-nothing dalam transaksi database.
```
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
```

Pendekatan ini menjaga konsistensi data, memastikan bahwa semua proses berjalan utuh atau tidak sama sekali.

---
# 📺 Stored Function
Stored function digunakan untuk mengambil informasi penting dari database tanpa mengubah data. Fungsi ini seperti dashboard status sistem, yang hanya menampilkan, bukan memodifikasi.

![image](https://github.com/user-attachments/assets/9fccb3f9-1377-4db0-8c70-99d3da0c97e6)

✅ get_total_tried(p_resep_id): Mengembalikan jumlah user yang sudah mencoba resep tersebut.

✅ get_saved_count(p_user_id): Menghitung berapa resep yang sudah disimpan oleh pengguna.

Dengan pendekatan ini, logika business rule seperti “berapa kali resep dicoba” tetap konsisten di seluruh sistem dan dapat diakses baik dari aplikasi maupun dari procedure.


---
# 📦 Backup Otomatis
![image](https://github.com/user-attachments/assets/d1c56313-cfe3-47eb-8e10-ee62d52bad1c)
![image](https://github.com/user-attachments/assets/7268cab1-909c-40af-be4c-7e6432f7b94a)

Berikut adalah kodingan backup :
````
-- MariaDB dump 10.18  Distrib 10.4.17-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ceritarasa
-- ------------------------------------------------------
-- Server version	10.4.17-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `aktivitas` varchar(255) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_aktivitas`
--

LOCK TABLES `log_aktivitas` WRITE;
/*!40000 ALTER TABLE `log_aktivitas` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resep`
--

DROP TABLE IF EXISTS `resep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `bahan` text DEFAULT NULL,
  `langkah` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cuisine_type` varchar(50) DEFAULT 'Indonesian',
  `difficulty_level` enum('Easy','Medium','Hard') DEFAULT 'Easy',
  `cooking_time` int(11) DEFAULT 30,
  `servings` int(11) DEFAULT 4,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep`
--

LOCK TABLES `resep` WRITE;
/*!40000 ALTER TABLE `resep` DISABLE KEYS */;
/*!40000 ALTER TABLE `resep` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_resep_after_insert

AFTER INSERT ON resep

FOR EACH ROW

BEGIN

  INSERT INTO log_aktivitas(user_id, aktivitas)

  VALUES(NEW.user_id, CONCAT('Menambahkan resep: ', NEW.judul));

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `resep_dicoba`
--

DROP TABLE IF EXISTS `resep_dicoba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resep_dicoba` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `resep_id` int(11) NOT NULL,
  `tried_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resep_id` (`resep_id`),
  CONSTRAINT `resep_dicoba_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resep_dicoba_ibfk_2` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resep_dicoba`
--

LOCK TABLES `resep_dicoba` WRITE;
/*!40000 ALTER TABLE `resep_dicoba` DISABLE KEYS */;
/*!40000 ALTER TABLE `resep_dicoba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_recipes`
--

DROP TABLE IF EXISTS `saved_recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `resep_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_save` (`user_id`,`resep_id`),
  KEY `resep_id` (`resep_id`),
  CONSTRAINT `saved_recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_recipes_ibfk_2` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_recipes`
--

LOCK TABLES `saved_recipes` WRITE;
/*!40000 ALTER TABLE `saved_recipes` DISABLE KEYS */;
/*!40000 ALTER TABLE `saved_recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-14 10:50:47
```

