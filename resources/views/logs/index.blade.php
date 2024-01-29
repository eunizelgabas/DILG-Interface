<x-app-layout>
    <div class=" p-8 rounded-md w-full ">
        <div class=" flex items-center justify-between pb-6">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">List of Logs</h2>

            </div>
            <div class="flex items-center justify-between">
                <div class="lg:ml-40 ml-10 space-x-8 justify-between">
                    @if(session('message'))
                        <div class="alert bg-green-400 p-4">
                            {{session('message')}}
                        </div>
                    @endif

                    @if(session('error'))
                    <div class="alert bg-red-400 p-4">
                        {{session('error')}}
                    </div>
                @endif


                </div>
            </div>
        </div>
        <div class="flex-1 pr-4">
            <div class="relative md:w-1/3">
                <input type="search Employee"
                    class="w-full pl-10 pr-4 py-2 rounded-lg shadow focus:outline-none focus:shadow-outline text-gray-600 font-medium"
                    placeholder="Search...">
                <div class="absolute top-0 left-0 inline-flex items-center p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                        <circle cx="10" cy="10" r="7" />
                        <line x1="21" y1="21" x2="15" y2="15" />
                    </svg>
                </div>
            </div>
        </div>
        <div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    <table class="min-w-full leading-normal">
                        <thead>

                            <tr>

                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Timestamp
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Log Entry
                                </th>


                            </tr>
                        </thead>
                        <tbody >
                            @foreach($logEntries as $logEntry)
                                <tr class="border-b border-gray-200">

                                    <td class="px-5 py-2 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ $logEntry->formattedCreatedAt }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 bg-white text-sm">
                                        {{ $logEntry->log_entry }}
                                    </td>



                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
