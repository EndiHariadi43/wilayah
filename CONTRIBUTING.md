# Contributing
1. Import `db/wilayah.sql` (wajib) + `db/wilayah_level_1_2.sql` (opsional untuk peta/koordinat).
2. Salin `apps/inc/db.php.example` → `apps/inc/db.php`, isi kredensial lokal.
3. Jalankan: `php -S 0.0.0.0:8080 -t apps` → buka `http://localhost:8080`.
4. Lint: `php -l apps/**/*.php`.
5. Buat branch feature/fix, commit kecil & jelas, lalu buka PR.
