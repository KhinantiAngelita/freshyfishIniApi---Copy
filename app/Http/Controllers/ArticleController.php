<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
     // Menampilkan semua artikel
     public function index()
     {
         $articles = Article::with('user:ID_user,name')->get();
         return response()->json($articles, 200);
     }

    // Menampilkan artikel berdasarkan ID
    public function show($id)
    {
        $article = Article::with('user:ID_user,name')->find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        return response()->json($article, 200);
    }

    // Membuat artikel baru
    public function store(Request $request)
    {
        $user = Auth::user();

        // Periksa apakah user memiliki role pembeli (1) atau penjual (2)
        if ($user->ID_role != 1 && $user->ID_role != 2) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk membuat artikel'], 403);
        }

        // Validasi data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category_content' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Buat artikel baru
        $article = Article::create([
            'ID_user' => $user->ID_user,
            'title' => $validatedData['title'],
            'category_content' => $validatedData['category_content'],
            'content' => $validatedData['content'],
        ]);

        return response()->json($article, 201);
    }

     // Mengedit artikel
     public function update(Request $request, $id)
     {
         $user = Auth::user();
         $article = Article::find($id);

         if (!$article) {
             return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
         }

         // Periksa kepemilikan artikel
         if ($article->ID_user != $user->ID_user) {
             return response()->json(['message' => 'Anda tidak memiliki akses untuk mengedit artikel ini'], 403);
         }

         // Validasi data
         $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category_content' => 'required|string|max:255',
            'content' => 'required|string',
         ]);

         // Update artikel
         $article->update([
             'title' => $validatedData['title'] ?? $article->title,
             'category_content' => $validatedData['category_content'] ?? $article->category_content,
             'content' => $validatedData['content'] ?? $article->content,
         ]);

         return response()->json($article, 200);
     }


    // Menghapus artikel
    public function destroy($id)
    {
        $user = Auth::user();
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        // Periksa kepemilikan artikel
        if ($article->ID_user != $user->ID_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menghapus artikel ini'], 403);
        }

        $article->delete();
        return response()->json(['message' => 'Artikel berhasil dihapus'], 200);
    }

}
