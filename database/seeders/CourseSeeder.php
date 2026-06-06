<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\LessonSource;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create default Users (Admin and Student)
        $admin = User::updateOrCreate(
            ['email' => 'admin@codino.com'],
            [
                'name' => 'Kak Admin Codino',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'age' => 25
            ]
        );

        $student = User::updateOrCreate(
            ['email' => 'student@codino.com'],
            [
                'name' => 'Budi Coder Cilik',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'age' => 9
            ]
        );

        // 2. Create Course 1: Scratch Game Creator Cilik
        $course1 = Course::create([
            'title' => 'Scratch Game Creator Cilik',
            'slug' => 'scratch-game-creator-cilik',
            'description' => 'Kelas pemrograman ramah anak paling asyik! Di sini kita akan belajar dasar logika pemrograman komputer dengan cara menyusun balok visual (visual block coding) untuk membuat berbagai game seru buatanmu sendiri.',
            'age_range' => '7 - 10 Tahun',
            'prerequisites' => 'Bisa menggunakan mouse, keyboard komputer, dan membaca petunjuk dengan lancar.',
            'price' => 150000.00,
            'is_published' => true
        ]);

        // Course 1 - Module 1: Berkenalan dengan Scratch
        $module1_1 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Bab 1: Dunia Scratch Yang Seru!',
            'description' => 'Ayo berkenalan dengan interface Scratch desktop dan balok-balok koding pertamamu.',
            'order' => 1
        ]);

        // Lessons inside Module 1-1
        $lesson1_1_1 = Lesson::create([
            'module_id' => $module1_1->id,
            'title' => 'Mengenal Kucing Scratch dan Area Kerja',
            'description' => 'Video perkenalan workspace Scratch Desktop dan bagaimana cara menggerakkan kucing Scratch pertamamu.',
            'content_type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/tS7W4X6e21g',
            'order' => 1
        ]);

        LessonSource::create([
            'lesson_id' => $lesson1_1_1->id,
            'title' => 'Download Scratch Desktop App',
            'url' => 'https://scratch.mit.edu/download'
        ]);

        $lesson1_1_2 = Lesson::create([
            'module_id' => $module1_1->id,
            'title' => 'Ebook Panduan Visual Scratch Pemula',
            'description' => 'Buku petunjuk gambar interaktif penuh warna untuk memandu langkah koding balok visual pertamamu.',
            'content_type' => 'ebook',
            'ebook_path' => 'ebooks/scratch_pemula.pdf',
            'order' => 2
        ]);

        // Course 1 - Module 2: Bikin Game Pertama
        $module1_2 = Module::create([
            'course_id' => $course1->id,
            'title' => 'Bab 2: Membuat Game Labirin (Maze Game)',
            'description' => 'Saatnya mempraktikkan koding balok visual Scratch untuk mengontrol objek menghindari rintangan!',
            'order' => 2
        ]);

        $lesson1_2_1 = Lesson::create([
            'module_id' => $module1_2->id,
            'title' => 'Koding Kontrol Keyboard & Deteksi Dinding',
            'description' => 'Cara membuat karakter merespon tombol panah keyboard dan memantul ketika menyentuh dinding labirin.',
            'content_type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/n4dZ-J9l8lY',
            'order' => 1
        ]);

        LessonSource::create([
            'lesson_id' => $lesson1_2_1->id,
            'title' => 'Download Starter Pack Assets Labirin',
            'url' => 'https://scratch.mit.edu/projects/editor/'
        ]);

        // Create a Quiz inside Course 1 Module 1
        $quiz1 = Quiz::create([
            'course_id' => $course1->id,
            'module_id' => $module1_1->id,
            'title' => 'Kuis Seru: Balok Gerakan Scratch!',
            'description' => 'Uji ingatanmu tentang fungsi warna balok coding di Scratch!'
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'multiple_choice',
            'question_text' => 'Apa fungsi utama dari balok koding berwarna BIRU di Scratch?',
            'points' => 10,
            'options' => ['Untuk bersuara', 'Untuk bergerak/berpindah posisi', 'Untuk berganti kostum', 'Untuk mendeteksi tombol keyboard'],
            'correct_answer' => 'Untuk bergerak/berpindah posisi'
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'essay',
            'question_text' => 'Tuliskan pemahamanmu dengan bahasa sendiri: apa gunanya tombol bendera hijau (Green Flag) di Scratch?',
            'points' => 15
        ]);


        // 3. Create Course 2: Bikin Website Keren Pertama
        $course2 = Course::create([
            'title' => 'Bikin Website Keren Pertama dengan HTML & CSS',
            'slug' => 'website-keren-pertama',
            'description' => 'Belajar bahasa rahasia internet! Di kelas ini kamu akan dituntun langkah-demi-langkah menulis sintaks HTML dan CSS untuk mendesain halaman web profil pribadimu yang super keren.',
            'age_range' => '10 - 13 Tahun',
            'prerequisites' => 'Bisa mengetik dengan lancar di keyboard laptop atau komputer desktop.',
            'price' => 199000.00,
            'is_published' => true
        ]);

        // Course 2 - Module 1: Struktur Web HTML
        $module2_1 = Module::create([
            'course_id' => $course2->id,
            'title' => 'Bab 1: Kerangka Web HTML',
            'description' => 'Mengenal tag-tag pembentuk struktur website pertamamu.',
            'order' => 1
        ]);

        $lesson2_1_1 = Lesson::create([
            'module_id' => $module2_1->id,
            'title' => 'Mengenal Tag Heading, Paragraph, dan Image',
            'description' => 'Tutorial memasukkan judul, tulisan paragraf panjang, serta gambar profil pertamamu di dokumen HTML.',
            'content_type' => 'video',
            'video_url' => 'https://www.youtube.com/embed/UB1O30zR-EE',
            'order' => 1
        ]);

        LessonSource::create([
            'lesson_id' => $lesson2_1_1->id,
            'title' => 'Download Editor VS Code gratis',
            'url' => 'https://code.visualstudio.com/'
        ]);
    }
}
