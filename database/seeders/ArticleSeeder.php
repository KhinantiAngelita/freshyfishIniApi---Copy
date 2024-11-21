<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Contoh data artikel
        $articles = [
            [
                'ID_user' => 1, // Pastikan ID_user ini ada di tabel users
                'title' => 'Panduan Beternak Ikan Lele',
                'category_content' => 'Ikan Payau',
                'content' => 'Artikel ini berisi tips dan trik untuk beternak ikan lele dengan efisien.',
            ],
            [
                'ID_user' => 3, // Pastikan ID_user ini ada di tabel users
                'title' => 'Manfaat Mengonsumsi Ikan Gurame',
                'category_content' => 'Ikan Tawar',
                'content' => 'Ikan segar mengandung banyak nutrisi yang bermanfaat untuk kesehatan.',
            ],
            [
                'ID_user' => 1,
                'title' => 'Memulai Bisnis Ikan Sardine',
                'category_content' => 'Ikan Lau',
                'content' => 'Langkah-langkah memulai bisnis ikan hias untuk pemula.',
            ],
        ];

        // Insert data ke tabel articles
        foreach ($articles as $article) {
            Article::create($article);
        }
    }
}
