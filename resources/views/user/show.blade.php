<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Show User ') }}
        </h2>
    </x-slot>

    <div class='w-full max-w-md mt-20 mx-auto bg-white rounded-3xl shadow-xl overflow-hidden'>
        <div class="max-w-md mx-auto relative">
            <div class="bg-cover bg-center h-[200px]" style="background-image: url('{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/boy.jpg') }}');">
            </div>
            <div class="p-4 sm:p-6 relative z-10">
              <p class="font-bold text-gray-700 text-[22px] leading-7 mb-1">{{$user->name}}</p>
              <div class="flex flex-row">
                @if($user->status == 1)
                <span class="remarks-cell py-1 px-3 rounded-full text-xs bg-green-200 text-green-600">Active</span>
                @else
                <span class="text-md font-semibold remarks-cell py-1 px-3 rounded-full text-xs bg-red-200 text-red-600">Inactive</span> <br>
                @endif
                <span class="text-md font-semibold ml-4  mr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                  </svg>
                </span>{{$user->email}}
              </div>
              @if($user->status == 1)
              <form method="POST" action="{{ route('user.deactivate', ['user' => $user->id]) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="block mt-10 w-full px-4 py-3 font-medium tracking-wide text-center capitalize transition-colors duration-300 transform bg-blue-500 rounded-[14px] hover:bg-blue-600 focus:outline-none focus:ring focus:ring-teal-300 focus:ring-opacity-80">
                  Deactivate
                </button>
              </form>
              @else
              <form method="POST" action="{{ route('user.activate', ['user' => $user->id]) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="block mt-10 w-full px-4 py-3 font-medium tracking-wide text-center capitalize transition-colors duration-300 transform bg-green-500 rounded-[14px] hover:bg-green-600 focus:outline-none focus:ring focus:ring-teal-300 focus:ring-opacity-80">
                  Activate
                </button>
              </form>
              @endif
              {{-- <a href="https://apps.apple.com/us/app/id1493631471" class="block mt-1.5 w-full px-4 py-3 font-medium tracking-wide text-center capitalize transition-colors duration-300 transform bg-red-400 rounded-[14px] hover:bg-red-600 hover:text-[#000000dd] focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80">
                Delete Account
              </a> --}}
              <a onclick="openDeleteModal({{ $user->id }})" as="button" class="block mt-1.5 w-full px-4 py-3 font-medium tracking-wide text-center capitalize transition-colors duration-300 transform bg-red-400 rounded-[14px] hover:bg-red-600 hover:text-[#000000dd] focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80 cursor-pointer">
                Delete
            </a>
            <div id="modelConfirm" class="fixed hidden z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 ">
                <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md">

                    <div class="flex justify-end p-2">
                        <button onclick="closeModal('modelConfirm')" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 pt-0 text-center">
                        <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-normal text-gray-500 mt-5 mb-6">Are you sure you want to delete this user?</h3>
                        <div class="flex  justify-center">
                            <form id="deleteForm" method="POST" action="{{ url('/users/'. $user->id) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" onclick="closeModal('modelConfirm')"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            </form>
                            <a href="#" onclick="closeModal('modelConfirm')"
                                class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-cyan-200 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center"
                                data-modal-toggle="delete-user-modal">
                                No, cancel
                            </a>
                        </div>

                    </div>

                </div>
            </div>
            </div>
          </div>

    </div>


</x-app-layout>

<script>
    function openDeleteModal(userId) {
    // Show the modal
    var modal = document.getElementById("modelConfirm");
    modal.classList.remove("hidden");
}

// Function to close the delete modal
function closeModal(modalId) {
    // Hide the modal
    var modal = document.getElementById(modalId);
    modal.classList.add("hidden");
}
</script>
