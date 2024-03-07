<x-app-layout>

    <div class=" flex justify-center items-center mt-8">
        <div class="lg:w-1/2 md:w-1/2 w-1/2">
            <form method="POST" action="{{ route('user.store') }}" class="bg-white p-10 rounded-lg shadow-lg min-w-full" enctype="multipart/form-data">
                @csrf
                <h1 class="text-center text-2xl mb-6 text-gray-600 font-bold font-sans">Create User</h1>
                <div class="mb-5 text-center">
                    <div class="mx-auto w-32 h-32 mb-2 border rounded-full relative bg-gray-100 mb-4 shadow-inset">
                        <img id="avatar-preview" class="object-cover w-full h-32 rounded-full" src="asset('images/boy.jpg')" />
                    </div>

                    {{-- <label for="avatar" class="cursor-pointer inline-flex justify-between items-center focus:outline-none border py-2 px-4 rounded-lg shadow-sm text-left text-gray-600 bg-white hover:bg-gray-100 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-flex flex-shrink-0 w-6 h-6 -mt-1 mr-1" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                            <path d="M5 7h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>
                        Browse Photo
                    </label>

                    <div class="mx-auto w-48 text-gray-500 text-xs text-center mt-1">Click to add profile picture</div>

                    <input name="avatar" id="avatar" accept="image/*" class="hidden" type="file" onchange="previewAvatar(event)"> --}}
                    @error('name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="name">Name</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="text" name="name" id="name"  />
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="role">Roles</label>
                    <select name="role" id="role"  class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none ">
                        <option >Select a Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="email">Email</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="text" name="email" id="email"  />
                    @error('email')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="password">Password</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="password" name="password" id="password"  />
                    @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="password_confirmation">Password Confirmation</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="password" name="password_confirmation" id="password_confirmation"  />
                    @error('password_confirmation')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                </div>
                <button type="submit" class="w-full mt-6 mb-3 bg-blue-500  hover:bg-blue-600 rounded-lg px-4 py-2 text-lg text-gray-800 tracking-wide font-semibold font-sans">Create account</button>
            </form>
        </div>
    </div>
    </x-app-layout>
    <script>
        // Set default image source
        const defaultAvatar = "{{ asset('images/boy.jpg') }}";
        document.getElementById('avatar-preview').src = defaultAvatar;

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
                // If no file is selected, revert to the default image
                preview.src = defaultAvatar;
            }
        }
    </script>

