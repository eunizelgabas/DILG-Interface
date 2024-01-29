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

        <div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                    @if(count($logEntries) > 0)
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
                <div class="d-flex justify-content-end mt-2 ">
                    {{ $logEntries->onEachSide(1)->links() }}
                </div>
                @else
                <div class="flex justify-center items-center">
                    <h1>No system log available</h1>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
