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

---
# 🔄 Transaction (Transaksi)

---
# 📺 Stored Function

---
# 📦 Backup Otomatis
