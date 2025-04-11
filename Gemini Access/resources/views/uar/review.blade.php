@extends('layouts.app')

@section('content')
<div class="max-w-6xl mt-8 mx-auto bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Review User Access - {{ $uar->application }}</h2>
        @if ($uar->users && $uar->users->isNotEmpty())
            <form action="{{ route('uar.approveAll', $uar->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Approve All</button>
            </form>
        @endif
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                @if ($uar->users && $uar->users->isNotEmpty())
                    @php
                        $columns = array_keys($uar->users->first()->user_data ?? []);
                    @endphp
                    @foreach ($columns as $column)
                        <th class="border p-2">{{ ucfirst($column) }}</th>
                    @endforeach
                @endif
                <th class="border p-2">Primary Reviewer Status</th>
                <th class="border p-2">Secondary Reviewer Status</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
        @if ($uar->users && $uar->users->isNotEmpty())
            @foreach ($uar->users as $user)
                <tr>
                    @foreach ($columns as $column)
                        <td class="border p-2">{{ $user->user_data[$column] ?? 'N/A' }}</td>
                    @endforeach
                    <td class="border p-2">{{ ucfirst($user->primary_review_status ?? 'Pending') }}</td>
                    <td class="border p-2">{{ ucfirst($user->secondary_review_status ?? 'Pending') }}</td>
                    <td class="border p-2">
                        <form action="{{ route('uar.approve', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-green-500 text-white rounded">Approve</button>
                        </form>
                        <form action="{{ route('uar.reject', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded">Reject</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" class="border p-2 text-center">No users found.</td>
            </tr>
        @endif
        </tbody>
    </table>
                   
    
</div>
@endsection
