@extends('layouts.app')

@section('content')
<div class="max-w-4xl mt-8 mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Upload User Listing & Screenshot</h2>

    @if ($uar->files->isNotEmpty())
    @php
        $file = $uar->files->last(); // Get the most recent file (if needed)
    @endphp
    <p><strong>Uploaded User List:</strong> <a href="{{ Storage::url($file->user_list) }}" class="text-blue-500" target="_blank">View File</a></p>
    <p><strong>Uploaded Screenshot:</strong> <a href="{{ Storage::url($file->screenshot) }}" class="text-blue-500" target="_blank">View File</a></p>
    
    <p class="mt-4 text-gray-600">If you want to replace the files, upload new ones below:</p>
@endif

    <form action="{{ route('uar.upload', $uar->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">User List (Excel)</label>
            <input type="file" name="user_list" accept=".csv,.xlsx" {{ $uar->file ? '' : 'required' }} class="border p-2 w-full">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Screenshot (PDF/JPG/DOCX)</label>
            <input type="file" name="screenshot" accept=".pdf,.jpg,.jpeg,.png,.docx" {{ $uar->file ? '' : 'required' }} class="border p-2 w-full">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
            {{ $uar->file ? 'Replace Files' : 'Upload' }}
        </button>
    </form>
</div>
@endsection
