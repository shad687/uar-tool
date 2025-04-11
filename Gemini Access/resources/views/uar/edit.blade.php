@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mt-8 mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit UAR - {{ $uar->application }}</h2>

        <form action="{{ route('uar.update', $uar->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label class="block text-sm font-medium">Application Name</label>
            <input type="text" name="application" value="{{ old('application', $uar->application) }}" required
                class="w-full border p-2 rounded">

            <label class="block text-sm font-medium mt-2">Application Owner</label>
            <select name="application_owner" required class="w-full border p-2 rounded">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $uar->app_owner_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <label class="block text-sm font-medium mt-2">Primary Reviewer</label>
            <select name="primary_reviewer" required class="w-full border p-2 rounded">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $uar->primary_reviewer_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <label class="block text-sm font-medium mt-2">Secondary Reviewer</label>
            <select name="secondary_reviewer" required class="w-full border p-2 rounded">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $uar->secondary_reviewer_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <label class="block text-sm font-medium mt-2">Frequency</label>
            <select name="frequency" required class="w-full border p-2 rounded">
                <option value="monthly" {{ $uar->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="quarterly" {{ $uar->frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="semiannual" {{ $uar->frequency == 'semiannual' ? 'selected' : '' }}>Semiannual</option>
                <option value="annual" {{ $uar->frequency == 'annual' ? 'selected' : '' }}>Annual</option>
            </select>

            <label class="block text-sm font-medium mt-2">Start Date</label>
            <input type="date" name="start_at" value="{{ old('start_at', $uar->start_at) }}" required
                class="w-full border p-2 rounded">

            <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Update UAR</button>
        </form>
    </div>
@endsection