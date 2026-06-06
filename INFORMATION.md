# INFORMATION.md - Kid Coding Online Class Platform

Dokumen ini berisi informasi arsitektur, spesifikasi, dan kebutuhan sistem untuk platform kelas online pemrograman anak. Dokumen ini ditujukan untuk mempermudah AI Agent atau developer memahami struktur proyek saat bekerja.

---

## 1. Overview Proyek
* **Nama Platform:** Codino (atau sejenisnya)
* **Target Pengguna:** Anak-anak (misal usia 7-15 tahun) & Orang Tua.
* **Teknologi Utama:** 
  * **Backend & Frontend:** Laravel (dengan Blade Templates).
  * **Database:** MySQL.
  * **Styling:** Vanilla CSS / Bootstrap (sesuai kebutuhan, namun frontend murni Blade & CSS tanpa framework JS kompleks seperti React/Vue).

---

## 2. Fitur Utama
1. **Autentikasi & Otorisasi:**
   * Registrasi, Login, dan Logout.
   * Middleware untuk memisahkan hak akses **Admin** dan **Peserta (Student)**.
2. **Manajemen Kelas & Peserta (Admin Panel):**
   * CRUD data Kelas (Course).
   * CRUD data Peserta (Student).
   * Monitoring pendaftaran (Enrollment).
3. **Detail Kelas & Materi Belajar:**
   * Struktur Kelas dibagi menjadi beberapa **Modul** dan setiap modul memiliki beberapa **Materi (Lesson)**.
   * Materi dapat berisi:
     * Video (Upload langsung / YouTube Link).
     * Ebook (File PDF / EPUB).
     * Section Source (Link source code, download aplikasi pendukung, atau asset project).
   * Detail metadata kelas:
     * Batasan Usia (Age Range).
     * Prasyarat Kelas (Pre-requisites).
     * Harga Kelas (Price).
     * Judul, deskripsi lengkap, & banner kelas.
4. **Sistem Kuis Interaktif (Quiz):**
   * Kuis dapat diselipkan di antara modul atau lesson.
   * Jenis soal yang didukung:
     * Pilihan Ganda (Multiple Choice).
     * Essay (Jawaban panjang).
     * Coding Test (Instruksi menulis kode/teks kode sederhana).
     * File Submission (Link atau upload file untuk project coding anak).
   * Penilaian kuis (grading) dan feedback dari Admin.

---

## 3. Desain Database (Skema Relasional)

### Tabel `users`
Menyimpan data pengguna (Admin & Peserta/Student).
* `id` (Primary Key)
* `name` (varchar)
* `email` (varchar, unique)
* `password` (varchar)
* `role` (enum: `'admin'`, `'student'`)
* `age` (integer, nullable - untuk data peserta anak)
* `created_at` / `updated_at`

### Tabel `courses`
Menyimpan data kelas pemrograman.
* `id` (Primary Key)
* `title` (varchar)
* `slug` (varchar, unique)
* `description` (text)
* `age_range` (varchar, contoh: "8-12 tahun")
* `prerequisites` (text, contoh: "Bisa mengetik, mengerti logika dasar")
* `price` (decimal, contoh: `150000.00`)
* `cover_image` (varchar, path file banner kelas)
* `is_published` (boolean, default: false)
* `created_at` / `updated_at`

### Tabel `modules`
Menyimpan modul di dalam suatu kelas (opsional tapi disarankan untuk merapikan materi).
* `id` (Primary Key)
* `course_id` (Foreign Key -> `courses.id`, cascade delete)
* `title` (varchar)
* `description` (text, nullable)
* `order` (integer, urutan modul)
* `created_at` / `updated_at`

### Tabel `lessons`
Menyimpan materi pelajaran di dalam modul.
* `id` (Primary Key)
* `module_id` (Foreign Key -> `modules.id`, cascade delete)
* `title` (varchar)
* `description` (text, nullable)
* `content_type` (enum: `'video'`, `'ebook'`, `'text'`)
* `video_url` (varchar, nullable - untuk YouTube link atau storage path)
* `ebook_path` (varchar, nullable - path PDF/ebook di storage)
* `order` (integer, urutan materi)
* `created_at` / `updated_at`

### Tabel `lesson_sources`
Menyimpan link download resource coding (Source code, download tools/aplikasi, asset gambar/sound).
* `id` (Primary Key)
* `lesson_id` (Foreign Key -> `lessons.id`, cascade delete)
* `title` (varchar, contoh: "Download Scratch Desktop", "Download Assets Project")
* `url` (varchar, link download atau link eksternal)
* `created_at` / `updated_at`

### Tabel `quizzes`
Menyimpan metadata kuis yang disisipkan di dalam kelas/modul.
* `id` (Primary Key)
* `course_id` (Foreign Key -> `courses.id`, cascade delete)
* `module_id` (Foreign Key -> `modules.id`, nullable, jika kuis per modul)
* `title` (varchar)
* `description` (text, nullable)
* `created_at` / `updated_at`

### Tabel `quiz_questions`
Menyimpan daftar soal dalam kuis.
* `id` (Primary Key)
* `quiz_id` (Foreign Key -> `quizzes.id`, cascade delete)
* `type` (enum: `'multiple_choice'`, `'essay'`, `'coding_test'`, `'submission'`)
* `question_text` (text)
* `points` (integer, bobot nilai)
* `options` (json, nullable - berisi opsi pilihan ganda A, B, C, D)
* `correct_answer` (text, nullable - jawaban benar untuk auto-grading pilihan ganda)
* `created_at` / `updated_at`

### Tabel `enrollments`
Menyimpan status keikutsertaan peserta di suatu kelas.
* `id` (Primary Key)
* `user_id` (Foreign Key -> `users.id`, cascade delete)
* `course_id` (Foreign Key -> `courses.id`, cascade delete)
* `payment_status` (enum: `'pending'`, `'paid'`, `'cancelled'`)
* `paid_amount` (decimal)
* `created_at` / `updated_at`

### Tabel `quiz_submissions`
Menyimpan jawaban peserta untuk suatu kuis.
* `id` (Primary Key)
* `quiz_id` (Foreign Key -> `quizzes.id`, cascade delete)
* `user_id` (Foreign Key -> `users.id`, cascade delete)
* `score` (integer, nullable - diisi setelah dinilai)
* `status` (enum: `'pending'`, `'graded'`)
* `feedback` (text, nullable - masukan dari admin)
* `created_at` / `updated_at`

### Tabel `quiz_answers`
Menyimpan detail jawaban per soal dari submission kuis.
* `id` (Primary Key)
* `submission_id` (Foreign Key -> `quiz_submissions.id`, cascade delete)
* `question_id` (Foreign Key -> `quiz_questions.id`, cascade delete)
* `answer_text` (text, nullable - untuk essay/coding)
* `selected_option` (varchar, nullable - untuk pilihan ganda)
* `file_path` (varchar, nullable - jika mengirimkan submission file/link)
* `points_awarded` (integer, nullable - nilai yang diberikan)
* `created_at` / `updated_at`

---

## 4. Alur Kerja & Hak Akses (Middleware)

### Admin Role
* Mengelola data Kelas, Modul, dan Lesson.
* Menambahkan video (URL/Upload), Ebook, dan Link Source pendukung.
* Membuat Kuis beserta tipe pertanyaannya.
* Memantau daftar Peserta dan memvalidasi pembayaran/enrollment.
* Memeriksa dan memberikan nilai/feedback pada hasil Kuis (terutama tipe essay, coding_test, dan submission).

### Student Role
* Melakukan registrasi & login.
* Memilih dan melakukan pendaftaran kelas (Enrollment).
* Mengakses materi belajar (menonton video, mendownload ebook/source code).
* Mengerjakan kuis yang disediakan dan melihat hasil nilai serta feedback.

---

## 5. Rencana Dependensi / Paket Laravel Tambahan
* **Laravel Jetstream** (Opsional, untuk scaffolding auth berbasis Blade agar cepat dan clean).
