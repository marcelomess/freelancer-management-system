<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Desenvolvimento Web',
            'Design Gráfico',
            'Marketing Digital',
            'Redação',
            'Tradução',
            'Vídeo e Animação',
            'Fotografia',
            'Consultoria',
            'Suporte Técnico',
            'Mobile',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
