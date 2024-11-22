<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Menampilkan semua artikel
    public function index()
    {
        $articles = Article::with('user:id,name')->get();
        return response()->json($articles, 200);
    }

    // Menampilkan artikel berdasarkan ID
    public function show($id)
    {
        $article = Article::with('user:id,name')->find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        // Menambahkan URL untuk foto artikel jika ada
        if ($article->photo_content) {
            $article->photo_url = Storage::url('public/articles/' . $article->photo_content);
        }

        return response()->json($article, 200);
    }

    // Membuat artikel baru
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category_content' => 'required|string', // Validasi untuk category_content
            'content' => 'required|string', // Validasi untuk content
            'photo_content' => 'required|image|mimes:jpg,png,jpeg', // Validasi file gambar
        ]);

        // Menyimpan file foto artikel
        $photo = $request->file('photo_content');
        $photoName = time() . '.' . $photo->getClientOriginalExtension();
        $photo->storeAs('public/articles', $photoName); // Menyimpan foto di folder 'public/articles'

        // Menyimpan artikel
        $article = Article::create([
            'ID_user' => $user->ID_user,
            'title' => $validatedData['title'],
            'category_content' => $validatedData['category_content'],
            'content' => $validatedData['content'],
            'photo_content' => $photoName, // Menyimpan nama file foto
        ]);

        return response()->json($article, 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        $user = Auth::user();
        if ($article->ID_user != $user->ID_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengedit artikel ini'], 403);
        }

        // Cek apakah data diterima
        \Log::info($request->all()); // Log data request yang diterima

        // Validasi data artikel yang diterima
        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_content' => 'sometimes|string',
            'content' => 'sometimes|string',
            'photo_content' => 'sometimes|image|mimes:jpg,png,jpeg',
        ]);

        if ($request->hasFile('photo_content')) {
            if ($article->photo_content && Storage::exists('public/articles/' . $article->photo_content)) {
                Storage::delete('public/articles/' . $article->photo_content);
            }

            $photo = $request->file('photo_content');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/articles', $photoName);

            $validatedData['photo_content'] = $photoName;
        }

        $article->update([
            'title' => $validatedData['title'] ?? $article->title,
            'category_content' => $validatedData['category_content'] ?? $article->category_content,
            'content' => $validatedData['content'] ?? $article->content,
            'photo_content' => $validatedData['photo_content'] ?? $article->photo_content,
        ]);

        return response()->json($article);
    }

    // Menghapus artikel
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        $user = Auth::user();

        // Cek apakah pengguna memiliki akses untuk menghapus artikel
        if ($article->ID_user != $user->ID_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menghapus artikel ini'], 403);
        }

        // Hapus foto artikel jika ada
        if ($article->photo_content && Storage::exists('public/articles/' . $article->photo_content)) {
            Storage::delete('public/articles/' . $article->photo_content);
        }

        $article->delete();

        return response()->json(['message' => 'Artikel berhasil dihapus'], 200);
    }
}
