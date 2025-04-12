<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Users') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-8 p-6 bg-white shadow rounded-lg">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif


        <div class="flex justify-end mb-4">
            <button onclick="exportTableToExcel('userTable')"
                class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                Export to Excel
            </button>
        </div>

        <table id="userTable" class="w-full table-auto border-collapse">

            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Admin</th>
                    <th class="p-2">Associated UARs</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                        <td class="p-2">
                            <ul class="list-disc ml-4 text-sm">
                                @foreach($user->uarRoles as $role)
                                    <li>{{ $role }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('users.toggle-admin', $user) }}">
                                @csrf
                                <button class="bg-indigo-500 hover:bg-indigo-700 text-white px-3 py-1 rounded">
                                    {{ $user->is_admin ? 'Revoke Admin' : 'Make Admin' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
<script>
    function exportTableToExcel(tableID, filename = '') {
        let dataType = 'application/vnd.ms-excel';
        let tableSelect = document.getElementById(tableID);
        let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        filename = filename ? filename + '.xls' : 'users_export.xls';

        let downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            let blob = new Blob(['\ufeff', tableHTML], { type: dataType });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;
            downloadLink.click();
        }
    }
</script>