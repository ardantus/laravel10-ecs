@extends('layouts.app')

@section('content')
<div>
    <h2>Folders</h2>

    <!-- Form untuk membuat folder baru -->
    <form action="{{ url('/folders') }}" method="POST" style="margin-bottom: 20px;">
        @csrf
        <input type="text" name="name" placeholder="Folder Name" required>
        <button type="submit">Create Folder</button>
    </form>

    <div>
        <h3>Existing Folders:</h3>
        @if ($folders->isEmpty())
            <p>No folders available.</p>
        @else
            @foreach ($folders as $folder)
            <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 10px;">
                <h4>
                    {{ $folder->name }}
                    <!-- Tombol hapus folder -->
                    <form action="{{ url("/folders/{$folder->id}") }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this folder and all its files?')">Delete Folder</button>
                    </form>
                </h4>

                <!-- Form untuk upload file ke folder -->
                <form action="{{ url("/folders/{$folder->id}/upload") }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 10px;">
                    @csrf
                    <input type="file" name="file" required>
                    <button type="submit">Upload File</button>
                </form>

                <!-- Daftar file di dalam folder -->
                @if ($folder->files->isEmpty())
                    <p>No files in this folder.</p>
                @else
                    <ul>
                        @foreach ($folder->files as $file)
                        <li style="margin-bottom: 10px;">
                            <strong>{{ $file->name }}</strong> ({{ $file->path }})
                            <!-- Tombol hapus file -->
                            <form action="{{ url("/files/{$file->id}") }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>

                            <!-- Preview gambar atau video -->
                            @php
                                $fileUrl = Storage::disk('s3')->url($file->path);
                                $isImage = Str::endsWith($file->path, ['.jpg', '.jpeg', '.png', '.gif']);
                                $isVideo = Str::endsWith($file->path, ['.mp4']);
                            @endphp

                            @if ($isImage)
                                <div>
                                    <img src="{{ $fileUrl }}" alt="{{ $file->name }}" style="max-width: 200px; margin-top: 10px;">
                                </div>
                            @elseif ($isVideo)
                                <div>
                                    <video controls style="max-width: 300px; margin-top: 10px;">
                                        <source src="{{ $fileUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
