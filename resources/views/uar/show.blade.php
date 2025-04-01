@extends('layouts.app')

@section('content')
<div class="max-w-4xl mt-8 mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">{{ $uar->application }}</h2>

    <p><strong>Application Owner:</strong> {{ $uar->appOwner->name ?? 'N/A' }}</p>
    <p><strong>Primary Reviewer:</strong> {{ $uar->primaryReviewer->name ?? 'N/A' }}</p>
    <p><strong>Secondary Reviewer:</strong> {{ $uar->secondaryReviewer->name ?? 'N/A' }}</p>
    <p><strong>Frequency:</strong> {{ ucfirst($uar->frequency) }}</p>
    <p><strong>Start At:</strong> {{ \Carbon\Carbon::parse($uar->start_at)->format('d M, Y') }}</p>
    <p><strong>Next Due:</strong> {{ \Carbon\Carbon::parse($uar->next_due)->format('d M, Y') }}</p>

    <div class="mt-4">
        <a href="{{ route('uar.edit', $uar->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
        <form action="{{ route('uar.destroy', $uar->id) }}" method="POST" class="inline-block ml-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </div>
</div>
@endsection
