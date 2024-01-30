<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List of Users') }}
        </h2>
    </x-slot>
    @if(session('success'))
        <div id="flash-message" class="bg-green-200 px-6 py-4 my-4 rounded-md text-lg flex items-center mx-auto max-w-lg absolute top-10 right-0">
            <svg viewBox="0 0 24 24" class="text-green-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                <path fill="currentColor" d="M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z"></path>
            </svg>
            <span class="text-green-800 text-sm">{{ session('success') }}</span>
        </div>

        <script>
            setTimeout(function(){
                document.getElementById('flash-message').style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
        </script>
    @endif
    <!-- component -->
        <div class=" py-5 overflow-x-hidden px-4">
            <div class="align-middle rounded-tl-lg rounded-tr-lg inline-block w-full py-4 overflow-hidden bg-white shadow-lg px-12">
                <div class="flex justify-between">
                    {{-- <div class="inline-flex border rounded w-7/12 px-2 lg:px-6 h-12 bg-transparent">
                        <div class="flex flex-wrap items-stretch w-full h-full mb-6 relative">
                            <div class="flex">
                                <span class="flex items-center leading-normal bg-transparent rounded rounded-r-none border border-r-0 border-none lg:px-3 py-2 whitespace-no-wrap text-grey-dark text-sm">
                                    <svg width="18" height="18" class="w-4 lg:w-auto" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.11086 15.2217C12.0381 15.2217 15.2217 12.0381 15.2217 8.11086C15.2217 4.18364 12.0381 1 8.11086 1C4.18364 1 1 4.18364 1 8.11086C1 12.0381 4.18364 15.2217 8.11086 15.2217Z" stroke="#455A64" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M16.9993 16.9993L13.1328 13.1328" stroke="#455A64" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <form action="{{ route('user.index') }}" method="GET" class="mb-4" id="searchForm">
                                <input name="search" type="text" oninput="searchOnChange()" class="flex-shrink flex-grow flex-auto leading-normal tracking-wide w-px flex-1 border border-none border-l-0 rounded rounded-l-none px-3 relative focus:outline-none text-xxs lg:text-xs lg:text-base text-gray-500 font-thin" placeholder="Search">
                            </form>
                        </div>
                    </div> --}}
                    <div class="flex-1 pr-4">
                        <div class="relative md:w-1/3">
                            <form action="{{ route('user.index') }}" method="GET" class="mb-4" id="searchForm">
                                <input type="text" name="search" value="{{ $search }}"
                                       class="w-full pl-10 pr-4 py-2 rounded-lg shadow focus:outline-none focus:shadow-outline text-gray-600 font-medium"
                                       placeholder="Search..." oninput="searchOnChange()">
                                <div class="absolute top-0 left-0 inline-flex items-center p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                         stroke-linejoin="round">
                                        <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                                        <circle cx="10" cy="10" r="7"/>
                                        <line x1="21" y1="21" x2="15" y2="15"/>
                                    </svg>
                                </div>
                                <div class="absolute top-0 left-0 inline-flex items-center p-2">
                                    <div id="loadingIndicator" class="hidden loader"></div>
                                </div>
                            </form>


                        </div>
                    </div>
                        <div class="flex-end rounded ">
                            <a href="/users/create" type="button" class="text-white font-normal py-2 px-6 rounded-full transition duration-300 ease-in-out focus:outline-none focus:shadow-outline bg-blue-700 border border-blue-700 hover:bg-blue-900 hover:border-blue-900">Add User</a>
                        </div>
                </div>
            </div>
            <div class="align-middle inline-block min-w-full shadow overflow-hidden bg-white shadow-dashboard px-8 pt-3 rounded-bl-lg rounded-br-lg">
                @if(count($users) === 0 && !empty($search))
                <div class="text-gray-900 mt-4 justify-center">
                    No data available for your search query "{{ $search }}".
                </div>
            @endif

            @if(count($users) > 0)
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">ID</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Fullname</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Email</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Role</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Status</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Created At</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">

                        @foreach ($users as $user )
                        <tr >
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm leading-5 text-gray-800">{{$user->id}}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                <div class="text-sm leading-5 text-blue-900">{{$user->name}}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">{{$user->email}}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">   {{ $user->roles->first()->name }}</td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">
                                <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                    @if($user->status == 1)
                                    <span class="remarks-cell py-1 px-3 rounded-full text-xs bg-green-200 text-green-600">Active</span>
                                    @else
                                        <span class="text-md font-semibold remarks-cell py-1 px-3 rounded-full text-xs bg-red-200 text-red-600">Inactive</span>
                                    @endif

                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500 text-blue-900 text-sm leading-5"> {{ $user->created_at->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-500 text-sm leading-5">
                                <a href="{{ url('/users/show', $user->id) }}" type="button" class="px-5 py-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none">View Details</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                @else
                    <div class="flex justify-center items-center">
                        <h1>No user available</h1>
                    </div>
                @endif
                <div class="d-flex justify-content-end mt-5 mb-5 ">
                    {{ $users->onEachSide(1)->links() }}
                </div>

        </div>
    </div>
</x-app-layout>


<style scoped>

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left: 4px solid #3498db;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
}
</style>
<script>
      function searchOnChange() {
        var form = document.getElementById('searchForm');
        form.submit();
    }
</script>
