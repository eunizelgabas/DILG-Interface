<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Show User ') }}
        </h2>
    </x-slot> --}}
    <!-- component -->
<!-- This is an example component -->
<div class=' flex items-center justify-center  px-6 from-[#F9F5F3] via-[#F9F5F3] to-[#F9F5F3] bg-gradient-to-br '>
    <div class='w-full max-w-md  mx-auto bg-white rounded-3xl shadow-xl overflow-hidden'>
        <div class='max-w-md mx-auto'>
          <div class='h-[200px]' style='background-image:url(/images/boy.jpg);background-size:cover;background-position:center'>
           </div>
          <div class='p-4 sm:p-6'>
            <p class='font-bold text-gray-700 text-[22px] leading-7 mb-1'>{{$user->name}}</p>
            <div class='flex flex-row'>
                @if($user->status == 1)
                <span class="remarks-cell py-1 px-3 rounded-full text-xs bg-green-200 text-green-600">Active</span>
                @else
                    <span class="text-md font-semibold remarks-cell py-1 px-3 rounded-full text-xs bg-red-200 text-red-600">Inactive</span> <br>
                @endif
                <span class="text-md font-semibold ml-4  mr-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
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


            <a href="https://apps.apple.com/us/app/id1493631471" class='block mt-1.5 w-full px-4 py-3 font-medium tracking-wide text-center capitalize transition-colors duration-300 transform bg-red-400 rounded-[14px] hover:bg-red-600 hover:text-[#000000dd] focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80'>
                 Delete Account
              </a>
          </div>
        </div>
    </div>
</div>
</x-app-layout>
