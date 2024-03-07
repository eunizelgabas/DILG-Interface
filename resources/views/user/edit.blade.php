<x-app-layout>
    <div class="bg-white border border-4 rounded-lg mx-auto mt-18 shadow relative m-5 p-2.5 w-1/2">
        <div class="flex items-start justify-between p-2.5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit User Details
            </h3>
            <a href="/users" type="button"  class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="modal">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
        </div>

        <div class="p-3 space-y-3">
            <form  method="POST" action="{{route('user.update', $user->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-5 text-center">
                    <div class="mx-auto w-32 h-32 mb-2 border rounded-full relative bg-gray-100 mb-4 shadow-inset">
                        <img id="avatar-preview" class="object-cover w-full h-32 rounded-full" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/boy.jpg') }}" />
                    </div>

                    {{-- <label for="avatar" class="cursor-pointer inline-flex justify-between items-center focus:outline-none border py-2 px-4 rounded-lg shadow-sm text-left text-gray-600 bg-white hover:bg-gray-100 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-flex flex-shrink-0 w-6 h-6 -mt-1 mr-1" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                            <path d="M5 7h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>
                        Browse Photo
                    </label> --}}

                    {{-- <div class="mx-auto w-48 text-gray-500 text-xs text-center mt-1">Click to add profile picture</div> --}}

                    <input name="avatar" id="avatar" accept="image/*" class="hidden" type="file" onchange="previewAvatar(event)">
                </div>

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-6">
                        <label for="name" class="text-sm font-medium text-gray-900 block mb-2">Name</label>
                        <input type="text" name="name" id="name"  value="{{ old('user', $user->name) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="email" class="text-sm font-medium text-gray-900 block mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('user', $user->email) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('email')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                    </div>
                    {{-- <div>
                        <label for="role" class="text-gray-800 font-semibold block my-3 text-md">Role:</label>
                        <select id="role" name="role" required class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @if($user->hasRole($role->name)) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-span-6 sm:col-span-6">
                        <label class="text-gray-800 font-semibold block my-3 text-md" for="role">Roles</label>
                        <select name="role" id="role"  class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none ">
                            <option disabled>Select a Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" @if($user->hasRole($role->name)) selected @endif>{{ $role->name }}</option>
                        @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror

                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="password" class="text-sm font-medium text-gray-900 block mb-2">Password <span class="text-red-400"> (
                            Please leave this space blank if there are no alterations required.)</span> </label>
                        <input type="password" name="password" id="password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('password')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                         @enderror
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="password_confirmation" class="text-sm font-medium text-gray-900 block mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('password_confirmation')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                    </div>
                </div>
                <div class="p-3 border-t border-gray-200 rounded-b">
                    <button class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Save all</button>
                </div>
            </form>
        </div>


    </div>

</x-app-layout>

<script>
   function previewAvatar(event) {
        const preview = document.getElementById('avatar-preview');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/boy.jpg') }}";
        }
    }
</script>
