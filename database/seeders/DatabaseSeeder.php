<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'full_name' => 'Super Admin',
                'username' => 'admin',
                'email' => 'admin@mathery.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'id' => 2,
                'full_name' => 'Said Muhammad Naufal',
                'username' => '11001100',
                'email' => 'naufal@mathery.com',
                'password' => Hash::make('11001100'),
                'role' => 'student',
            ],
            [
                'id' => 3,
                'full_name' => 'Syauqi Bilqisthi',
                'username' => '22002200',
                'email' => 'syauqi@mathery.com',
                'password' => Hash::make('22002200'),
                'role' => 'student',
            ],
            [
                'id' => 4,
                'full_name' => 'Harits Nakhlah Putra',
                'username' => '33003300',
                'email' => 'harits@mathery.com',
                'password' => Hash::make('33003300'),
                'role' => 'student',
            ]
        ];

        $courses = [
            [
                'teacher_id' => 1,
                'name' => 'Informatika - PSIK 24 B',
                'code' => 'KAL-PSIK25A-FWR',
                'description' => 'Informatika',
            ],
            [
                'teacher_id' => 1,
                'name' => 'Kalkulus Diferensial - PSIK 25 A',
                'code' => 'KAL-PSIK25A-6IF',
                'description' => 'Kalkulus Diferensial',
            ],
            [
                'teacher_id' => 1,
                'name' => 'Matematika Dasar - PSIK 25 C',
                'code' => 'MAT-PSIK25C-JNI',
                'description' => 'Mata Kuliah Matematika Dasar di Program Studi Ilmu Komputer Angkatan 25 Kelas C',
            ]
        ];

        $topics = [
            [
                'class_id' => 1,
                'name' => 'Pertemuan 1 - Sistem Bilangan Real',
                'slug' => 'pertemuan-1-sistem-bilangan-real-111',
                'description' => 'Membahas Tuntas Sistem Bilangan Real',
                'meeting_date' => '2026-01-22',
            ],
            [
                'class_id' => 1,
                'name' => 'Pertemuan 2 Pertidaksamaan',
                'slug' => 'pertemuan-2-pertidaksamaan-111',
                'description' => 'Membahas Tuntas Pertidaksamaan',
                'meeting_date' => '2026-01-23',
            ],
            [
                'class_id' => 1,
                'name' => 'Pertemuan 3 - Fungsi',
                'slug' => 'pertemuan-3-fungsi-111',
                'description' => 'Membahas Tuntas Fungsi',
                'meeting_date' => '2026-01-24',
            ],
            [
                'class_id' => 2,
                'name' => 'Pertemuan 1 - Limit',
                'slug' => 'pertemuan-1-limit-111',
                'description' => 'Membahas Tuntas Limit',
                'meeting_date' => '2026-01-23',
            ],
            [
                'class_id' => 2,
                'name' => 'Pertemuan 2 Turunan',
                'slug' => 'pertemuan-2-turunan-111',
                'description' => 'Membahas Tuntas Turunan',
                'meeting_date' => '2026-01-24',
            ],
            [
                'class_id' => 2,
                'name' => 'Pertemuan 3 - Penggunaan Turunan',
                'slug' => 'pertemuan-3-penggunaan-turunan-111',
                'description' => 'Membahas Tuntas Penggunaan Turunan',
                'meeting_date' => '2026-01-25',
            ],
            [
                'class_id' => 3,
                'name' => 'Pertemuan 1 - Fungsi Transenden',
                'slug' => 'pertemuan-1-fungsi-transenden-111',
                'description' => 'Membahas Tuntas Fungsi Transenden',
                'meeting_date' => '2026-01-24',
            ],
            [
                'class_id' => 3,
                'name' => 'Pertemuan 2 Nilai Mutlak',
                'slug' => 'pertemuan-2-nilai-mutlak-111',
                'description' => 'Membahas Tuntas Nilai Mutlak',
                'meeting_date' => '2026-01-25',
            ],
            [
                'class_id' => 3,
                'name' => 'Pertemuan 3 - Integral',
                'slug' => 'pertemuan-3-integral-111',
                'description' => 'Membahas Tuntas Integral',
                'meeting_date' => '2026-01-26',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        foreach ($courses as $course) {
            Course::create($course);
        }

        foreach ($topics as $topic) {
            Topic::create($topic);
        }
    }
}
