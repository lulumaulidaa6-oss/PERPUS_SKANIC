<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageFileController extends Controller
{
    /**
     * Menampilkan atau mengalirkan file dari direktori storage publik secara aman.
     * Fitur ini memastikan file gambar cover buku, bukti peminjaman, dan bukti pengembalian
     * tetap bisa diakses meskipun berada di shared hosting (seperti InfinityFree / cPanel)
     * yang tidak mengizinkan atau tidak mendukung symbolic link (storage:link).
     */
    public function show(string $path): StreamedResponse
    {
        // 1. Validasi keamanan: cegah directory traversal
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(404);
        }

        // 2. Bersihkan slash di awal path
        $cleanPath = ltrim($path, '/');

        // 3. Lokasi pencarian file di berbagai skenario hosting
        $candidatePaths = [
            storage_path('app/public/'.$cleanPath),
            public_path('storage/'.$cleanPath),
            base_path('storage/app/public/'.$cleanPath),
        ];

        foreach ($candidatePaths as $fullPath) {
            if (file_exists($fullPath) && ! is_dir($fullPath)) {
                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                $mimeType = match ($ext) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'bmp' => 'image/bmp',
                    'pdf' => 'application/pdf',
                    default => function_exists('mime_content_type') ? (@mime_content_type($fullPath) ?: 'application/octet-stream') : 'application/octet-stream',
                };

                $size = @filesize($fullPath) ?: 0;
                $stream = fopen($fullPath, 'rb');

                return response()->stream(function () use ($stream) {
                    if (is_resource($stream)) {
                        fpassthru($stream);
                        fclose($stream);
                    }
                }, 200, array_filter([
                    'Content-Type' => $mimeType,
                    'Content-Length' => $size > 0 ? (string) $size : null,
                    'Cache-Control' => 'public, max-age=31536000',
                ]));
            }
        }

        abort(404);
    }

    /**
     * Menyinkronkan file yang baru diunggah ke folder public_path('storage') jika bukan symlink.
     * Ini memberikan kompatibilitas ganda di shared hosting agar web server bisa membaca langsung
     * atau melalui rute fallback di atas.
     */
    public static function syncToPublic(string $relativePath): void
    {
        $cleanPath = ltrim($relativePath, '/');
        $source = storage_path('app/public/'.$cleanPath);
        $dest = public_path('storage/'.$cleanPath);

        if (! file_exists($source)) {
            return;
        }

        // Jika tujuan dan sumber merujuk ke folder fisik yang sama (sudah ter-symlink), jangan salin
        if (file_exists($dest) && realpath($source) === realpath($dest)) {
            return;
        }

        $destDir = dirname($dest);
        if (! is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        @copy($source, $dest);
    }

    /**
     * Menghapus salinan file di public_path('storage') jika ada dan bukan symlink.
     */
    public static function deleteFromPublic(string $relativePath): void
    {
        $cleanPath = ltrim($relativePath, '/');
        $source = storage_path('app/public/'.$cleanPath);
        $dest = public_path('storage/'.$cleanPath);

        if (file_exists($dest) && realpath($source) !== realpath($dest)) {
            @unlink($dest);
        }
    }
}
