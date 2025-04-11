@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-8 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Create User Access Review (UAR)</h2>
    
    <form action="{{ route('uar.store') }}" method="POST">
        @csrf
        
        <!-- Application Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Application</label>
            <input type="text" name="application" required class="w-full px-3 py-2 border rounded-lg">
        </div>

        <!-- Application Owner (Dropdown) -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Application Owner</label>
            <select name="app_owner" required class="w-full px-3 py-2 border rounded-lg">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Primary Reviewer -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Primary Reviewer</label>
            <select name="primary_reviewer" required class="w-full px-3 py-2 border rounded-lg">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Secondary Reviewer -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Secondary Reviewer</label>
            <select name="secondary_reviewer" required class="w-full px-3 py-2 border rounded-lg">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Frequency -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Frequency</label>
            <select name="frequency" required class="w-full px-3 py-2 border rounded-lg">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="semiannual">Semiannual</option>
                <option value="annual">Annual</option>
            </select>
        </div>

        <!-- Start Date -->
        <div class="mb-4">
            <label class="block text-sm font-medium">Start At</label>
            <input type="date" name="start_at" required class="w-full px-3 py-2 border rounded-lg">
        </div>

        <!-- Hidden Next Due Date -->
        <input type="hidden" name="next_due" id="next_due">

        <!-- Submit Button -->
        <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-700">
            Submit
        </button>
    </form>
</div>

<script>
    document.querySelector("input[name='start_at']").addEventListener("change", function () {
        let startDate = new Date(this.value);
        let frequency = document.querySelector("select[name='frequency']").value;
        let nextDueDate = new Date(startDate);

        switch (frequency) {
            case "monthly":
                nextDueDate.setMonth(startDate.getMonth() + 1);
                break;
            case "quarterly":
                nextDueDate.setMonth(startDate.getMonth() + 3);
                break;
            case "semiannual":
                nextDueDate.setMonth(startDate.getMonth() + 6);
                break;
            case "annual":
                nextDueDate.setFullYear(startDate.getFullYear() + 1);
                break;
        }

        document.querySelector("input[name='next_due']").value = nextDueDate.toISOString().split('T')[0];
    });
</script>
@endsection
