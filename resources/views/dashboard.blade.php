<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
   
    <div class="py-12">
        @if(Auth::user()->is_admin)
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-start space-x-4 p-4">
                            <a href="{{ route('uar.create') }}"
                                class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">
                                Create UAR
                            </a>
                            <a href="{{ route('users.manage') }}" 
                                class="bg-blue-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">
                                Manage Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
  

    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-4">Upcoming User Access Reviews (UARs)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($upcomingUARs->isEmpty())
                <div class="col-span-3">
                    <p class="text-gray-600">No upcoming UARs.</p>
                </div>
            @endif
            @foreach($upcomingUARs ?? [] as $uar)
            <a href="{{ route('uar.upcoming.show', $uar->id) }}" class="block p-4 bg-white rounded-lg shadow-md">
            <div class="flex justify-between">
                <h3 class="text-lg font-semibold">{{ $uar->application }}</h3>
                <span class="text-sm px-2 py-1 rounded {{ \Carbon\Carbon::parse($uar->next_due)->isPast() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                    {{ \Carbon\Carbon::parse($uar->next_due)->isPast() ? 'Overdue' : 'Upcoming' }}
                </span>
            </div>
            <p class="text-gray-600">Due: {{ \Carbon\Carbon::parse($uar->next_due)->format('d M, Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @if(Auth::user()->is_admin)
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Active User Access Reviews (UARs)</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($uars->isEmpty())
                <div class="col-span-3">
                    <p class="text-gray-600">No active UARs.</p>
                </div>
            @endif      
            @foreach($uars as $uar)
                <a href="{{ route('uar.show', $uar->id) }}"
                    class="block p-4 bg-white shadow-md rounded-lg hover:shadow-lg transition">
                    <div class="flex justify-between">
                        <h3 class="text-lg font-semibold">{{ $uar->application }}</h3>
                        <span
                            class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ ucfirst($uar->frequency) }}</span>
                    </div>
                    <p class="text-gray-600 mt-2">Next Due: <span
                            class="font-semibold">{{ \Carbon\Carbon::parse($uar->next_due)->format('d M, Y') }}</span></p>
                </a>
            @endforeach
        </div>
    </div>
    @endif






</x-app-layout>