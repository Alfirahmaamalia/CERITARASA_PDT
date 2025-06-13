#  🍲 CeritaRasa – Platform Kolaboratif Resep Masakan

CeritaRasa adalah aplikasi berbasis web yang memungkinkan pengguna menyimpan, berbagi, dan memodifikasi resep masakan. Dibangun menggunakan PHP dan MySQL, platform ini menekankan prinsip kolaborasi dan kreatifitas dalam dunia memasak, serta menerapkan fitur-fitur SQL tingkat lanjut seperti transaction, stored procedure, trigger, dan stored function untuk menjaga konsistensi dan integritas data.

---
# ERD

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
