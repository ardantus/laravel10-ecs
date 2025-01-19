<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    public function index()
    {
        // Ambil semua folder beserta file terkait
        $folders = Folder::with('files')->get();

        // Generate URL file untuk preview
        foreach ($folders as $folder) {
            foreach ($folder->files as $file) {
                $file->url = Storage::disk('s3')->url($file->path);
            }
        }

        return view('folders.index', compact('folders'));
    }


    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required']);

        // Buat folder di database
        $folder = Folder::create(['name' => $request->name]);

        try {
            // Membuat folder dengan cara menyimpan dummy file kosong
            $dummyFilePath = "folders/{$folder->name}/.placeholder";
            Storage::disk('s3')->put($dummyFilePath, '');

            return back()->with('success', 'Folder created successfully!');
        } catch (\Exception $e) {
            // Jika gagal, hapus folder dari database
            $folder->delete();
            return back()->withErrors([
                'error' => 'Error creating folder in S3: ' . $e->getMessage(),
            ]);
        }
    }


    public function uploadFile(Request $request, Folder $folder)
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,mp4|max:5120', // Maksimal 5MB
        ]);

        $fileName = $request->file('file')->getClientOriginalName();
        $uniqueName = uniqid() . '_' . $fileName;
        $filePath = "folders/{$folder->name}/{$uniqueName}";

        try {
            // Upload file ke S3
            $path = Storage::disk('s3')->putFileAs(
                "folders/{$folder->name}",
                $request->file('file'),
                $uniqueName
            );

			// Pastikan file memiliki visibilitas public
			Storage::disk('s3')->setVisibility($filePath, 'public');
		
            // Simpan informasi file ke database
            $folder->files()->create([
                'name' => $fileName,
                'path' => $filePath,
            ]);

            return back()->with('success', 'File uploaded successfully!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error uploading file: ' . $e->getMessage(),
            ]);
        }
    }


    public function deleteFolder(Folder $folder)
    {
        try {
            // Hapus semua file dari S3
            foreach ($folder->files as $file) {
                Storage::disk('s3')->delete($file->path);
            }

            // Hapus folder dari S3
            Storage::disk('s3')->deleteDirectory("folders/{$folder->name}");

            // Hapus semua file dari database
            $folder->files()->delete();

            // Hapus folder dari database
            $folder->delete();

            return back()->with('success', 'Folder and all files deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error deleting folder: ' . $e->getMessage(),
            ]);
        }
    }


    public function deleteFile(File $file)
    {
        try {
            // Hapus file dari S3
            Storage::disk('s3')->delete($file->path);

            // Hapus dari database
            $file->delete();

            return back()->with('success', 'File deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error deleting file: ' . $e->getMessage(),
            ]);
        }
    }
}
