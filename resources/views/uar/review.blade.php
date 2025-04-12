@extends('layouts.app')

@section('content')
<!-- Modal -->
<div id="instructionModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h2 class="text-xl font-semibold mb-4">Reviewer Instructions</h2>
            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                <li>Please review each user and mark them as Approved or Rejected.</li>
                <li>If you're a secondary reviewer, you can act only after the primary review is complete.</li>
                <li>Once you approve/reject the last user, you’ll be automatically redirected to the dashboard.</li>
            </ul>
            <div class="mt-6 text-right">
                <button onclick="document.getElementById('instructionModal').classList.add('hidden')" class="bg-blue-500 text-white px-4 py-2 rounded">Got it!</button>
            </div>
        </div>
    </div>
</div>

<div class="max-w-6xl mt-8 mx-auto bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Review User Access - {{ $uar->application }}</h2>
        @if ($uar->users && $uar->users->isNotEmpty() && $uar->status != 'complete' && $uar->status != 'awaiting_removal')
            <form action="{{ route('uar.approveAll', $uar->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Approve All</button>
            </form>
        @endif
    </div>

    <!-- UAR Status Info -->
    <div class="mb-4 p-4 bg-gray-100 rounded text-gray-700">
        <strong>Status:</strong>
        @switch($uar->status)
            @case('primary_review')
                In Primary Review
                @break
            @case('secondary_review')
                Reviewed by Primary, Awaiting Secondary Review
                @break
            @case('awaiting_removal')
                Review Complete, Rejections in Process
                @break
            @case('complete')
                ✅ Review Completed
                @break
            @default
                Unknown
        @endswitch
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
        @php
            $authId = auth()->id();
            $isPrimary = $uar->primary_reviewer_id === $authId;
            $isSecondary = $uar->secondary_reviewer_id === $authId;
            $allPrimaryReviewed = $uar->users->every(fn($u) => $u->primary_review_status !== 'pending');
        @endphp

        @foreach ($uar->users as $user)
            <tr>
                @foreach ($columns as $column)
                    <td class="border p-2">{{ $user->user_data[$column] ?? 'N/A' }}</td>
                @endforeach
                <td class="border p-2">{{ ucfirst($user->primary_review_status ?? 'Pending') }}</td>
                <td class="border p-2">{{ ucfirst($user->secondary_review_status ?? 'Pending') }}</td>
                <td class="border p-2">
                    @if ($uar->status === 'complete' || $uar->status === 'awaiting_removal')
                        <button disabled class="px-4 py-1 bg-gray-300 text-white rounded">Approve</button>
                        <button disabled class="px-4 py-1 bg-gray-300 text-white rounded">Reject</button>
                    @elseif ($isPrimary && $user->primary_review_status === 'pending')
                        <form action="{{ route('uar.approve', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-green-500 text-white rounded">Approve</button>
                        </form>
                        <form action="{{ route('uar.reject', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded">Reject</button>
                        </form>
                    @elseif ($isSecondary && $allPrimaryReviewed && $user->secondary_review_status === 'pending')
                        <form action="{{ route('uar.approve', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-green-500 text-white rounded">Approve</button>
                        </form>
                        <form action="{{ route('uar.reject', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded">Reject</button>
                        </form>
                    @else
                        <button disabled class="px-4 py-1 bg-gray-300 text-white rounded">Approve</button>
                        <button disabled class="px-4 py-1 bg-gray-300 text-white rounded">Reject</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    window.onload = function() {
        document.getElementById('instructionModal').classList.remove('hidden');
    }
</script>
@endsection
