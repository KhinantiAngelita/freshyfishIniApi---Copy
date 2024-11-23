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
        return response()->json(Article::all());
        // $articles = Article::with('user:id,name')->get();
        // return response()->json($articles, 200);
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
        // Mencari artikel berdasarkan ID
        $article = Article::find($id);

        // Jika artikel tidak ditemukan
        if (!$article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        // Mengambil user yang sedang login
        $user = Auth::user();

        // Cek jika user yang login adalah pemilik artikel
        if ($article->ID_user != $user->ID_user) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengedit artikel ini'], 403);
        }

        // Log data request yang diterima untuk debugging
        \Log::info('Request Data:', ['data' => $request->all()]);

        // Validasi data yang diterima dari request
        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_content' => 'sometimes|string',
            'content' => 'sometimes|string',
            'photo_content' => 'sometimes|image|mimes:jpg,png,jpeg',
        ]);

        // Cek apakah ada file foto yang diupload
        if ($request->hasFile('photo_content')) {
            // Hapus foto lama jika ada
            if ($article->photo_content && Storage::exists('public/articles/' . $article->photo_content)) {
                Storage::delete('public/articles/' . $article->photo_content);
            }

            // Simpan file foto baru
            $photo = $request->file('photo_content');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/articles', $photoName);

            // Masukkan nama file foto ke validated data
            $validatedData['photo_content'] = $photoName;
        }

        // Update artikel dengan data yang sudah tervalidasi
        try {
            $article->update([
                'title' => $validatedData['title'] ?? $article->title,
                'category_content' => $validatedData['category_content'] ?? $article->category_content,
                'content' => $validatedData['content'] ?? $article->content,
                'photo_content' => $validatedData['photo_content'] ?? $article->photo_content,
            ]);
        } catch (\Exception $e) {
            // Log jika ada kesalahan saat update
            \Log::error('Update failed:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui artikel'], 500);
        }

        // Kembalikan artikel yang sudah terupdate sebagai response
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
