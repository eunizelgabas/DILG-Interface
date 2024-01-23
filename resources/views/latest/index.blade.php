<x-app-layout>

    <div class="px-2 mt-5">
        <div class="p-4 mx-2">
            <div class="flex ">
                <h1 class="text-3xl font-medium text-gray-700 "></h1>
                <a href="" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 mr-4" >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                      </svg>
                       Add issuance
                </a>
            </div>
        <div class="w-full px-2">
            <div class="h-12">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- <div class="p-6 text-gray-900">You're logged in!</div> -->

                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Id</th>
                                <th class="py-3 px-6 text-center">FullName</th>
                                <th class="py-3 px-6 text-center">Gender</th>
                                <th class="py-3 px-6 text-center">Type</th>
                                <th class="py-3 px-6 text-center">Email</th>
                                <th class="py-3 px-6 text-center">Contact No</th>
                                <th class="py-3 px-6 text-center">Role</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light" >

                            <tr  class="border-b border-gray-200 hover:bg-gray-100" >
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <div class="flex items-center">

                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium"></p>
                                    </div>
                                </td>

                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center" >
                                        <p class="font-medium"></p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium">
                                            <span class=" remarks-cell bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs"></span>
                                        </p>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center">
                                        <div class="flex item-center justify-center">
                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                <a href="" title="Show Details">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                            <a href=" " class="btn" title="Edit Doctor">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        </div>
                                        {{-- <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                            <a href="#"  class="btn" title="Delete Category">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                            <x-modal >
                                                <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                                    <!-- Icon -->
                                                    <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                                    </svg>
                                                    </span>
                                                    <!-- End Icon -->

                                                    <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                                        Delete User
                                                    </h3>
                                                    <p class="text-gray-500">
                                                        Are you sure you want like to delete this User?
                                                    </p>

                                                    <div class="mt-6 flex justify-center gap-x-4">
                                                        <x-secondary-button >Cancel</x-secondary-button>
                                                        <x-danger-button>Delete</x-danger-button>
                                                    </div>
                                                </div>
                                            </x-modal>
                                        </div> --}}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </div>
    </div>
</x-app-layout>
