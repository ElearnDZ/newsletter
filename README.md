# Newsletter
Simple newsletter app using Laravel framework.

# Kebutuhan Sistem

- PHP >= 5.6.4
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

# Kebutuhan Lain

- Supervisor untuk menajalankan Queue Laravel

# Instalasi

- Clone repositori untuk mendapatkan update terbaru.
- Ubah hak akses direktori ```storage``` dan ```bootstrap/cache``` agar dapat ditulisi oleh aplikasi.
- Jalankan perintah ```composer update``` untuk memperbarui package dan framework.
- Salin berkas ```.env.example``` menjadi ```.env``` dan ubah beberapa pengaturan di dalamnya sesuai dengan mesin yang digunakan.
- Jalankan perintah ```php artisan key:generate``` untuk membuat key baru.
- Jalankan perintah ```php artisan migrate``` dan ```php artisan db:seed``` untuk menjalankan migration dan menambahkan data dummy.
- Jalankan built-in server dengan perintah ```php artisan serve``` atau ```php -S localhost:8000```.
- Akses URL ```localhost:8000``` melalui peramban.