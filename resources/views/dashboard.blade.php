<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}
    <!-- https://codepen.io/robstinson/pen/MWexYPG -->
<div class="flex items-center text-gray-800 p-10">
    <!-- Component Start -->
    <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-6 w-full max-w-6xl mx-20">

        <!-- Tile 1 -->
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                  </svg>

            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$user}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Total Users</span>

                </div>
            </div>
        </div>

        <!-- Tile 2 -->
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$latest}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Latest Issuances</span>

                </div>
            </div>
        </div>

        <!-- Tile 3 -->
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$joint}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Joint Circulars</span>

                </div>
            </div>
        </div>

        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$memo}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Memo Circulars</span>

                </div>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$presidential}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Presidential Directives</span>

                </div>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$draft}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Draft Issuances</span>

                </div>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded" >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-blue-700">
                    <path d="M14 8.00002C15.1046 8.00002 16 7.10459 16 6.00002C16 4.89545 15.1046 4.00002 14 4.00002C12.8954 4.00002 12 4.89545 12 6.00002C12 7.10459 12.8954 8.00002 14 8.00002Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4 6H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M16 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8 14C9.10457 14 10 13.1046 10 12C10 10.8954 9.10457 10 8 10C6.89543 10 6 10.8954 6 12C6 13.1046 6.89543 14 8 14Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4 12H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M10 12H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M17 20C18.1046 20 19 19.1046 19 18C19 16.8955 18.1046 16 17 16C15.8954 16 15 16.8955 15 18C15 19.1046 15.8954 20 17 20Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M19 18H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$republic}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Republic Acts</span>

                </div>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded">
            <div class="flex flex-shrink-0 items-center justify-center bg-blue-200 h-16 w-16 rounded">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m7.875 14.25 1.214 1.942a2.25 2.25 0 0 0 1.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 0 1 1.872 1.002l.164.246a2.25 2.25 0 0 0 1.872 1.002h2.092a2.25 2.25 0 0 0 1.872-1.002l.164-.246A2.25 2.25 0 0 1 16.954 9h4.636M2.41 9a2.25 2.25 0 0 0-.16.832V12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 0 1 .382-.632l3.285-3.832a2.25 2.25 0 0 1 1.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0 0 21.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 0 0 2.25 2.25Z" />
                  </svg>
            </div>
            <div class="flex-grow flex flex-col ml-4">
                <span class="text-xl font-bold">{{$legal}}</span>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Legal Opinions</span>

                </div>
            </div>
        </div>

    </div>
    <!-- Component End  -->

</div>
<div class="pt-2 px-2" >
    <div class="w-full ">
       <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 xl:col-span-2 2xl:col-span-2">
            <div class="flex justify-between ">
                <h3 class="flex items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark">
                    <span class="mr-3 text-dark font-bold">Recent Uploaded Issuances</span>
                </h3>
                {{-- <a href="" class=" inline-block text-[.925rem] font-medium leading-normal text-right align-right cursor-pointer rounded-2xl transition-colors duration-150 ease-in-out text-blue-400 bg-light-dark border-light shadow-none border-0 py-2 px-5 sm:self-center">
                    See More
                </a> --}}
            </div>
            <div class="flex flex-col">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class=" rounded-lg divide-y divide-gray-200 ">

                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Reference No</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Title</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">URL Link</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200">
                                    @if (count($issuance) == 0)
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">
                                            No issuances found.
                                        </td>
                                    </tr>
                                    @endif

                                    @foreach($issuance as $is)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap overflow-hidden max-w-xs truncate text-sm font-medium text-gray-800">
                                            {{$is->reference_no}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap overflow-hidden max-w-xs truncate text-sm text-gray-800">
                                            {{$is->title}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap overflow-hidden max-w-xs truncate text-sm text-gray-800">
                                            {{$is->type}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap overflow-hidden max-w-xs truncate text-xs text-center font-bold uppercase">
                                            <a href="{{ $is->url_link }}" class="font-bold ml-1 hover:underline" target="_blank">
                                                <span class=" text-blue-500 font-light">{{ $is->url_link }}</span>
                                               </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                    </div>
                </div>
            </div>

        </div>




        </div>


</div>

</x-app-layout>

<style>
    .truncate {
        max-width: 150px; /* Adjust the value based on your preference */
    }
</style>
