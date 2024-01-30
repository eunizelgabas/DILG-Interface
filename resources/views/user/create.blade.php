<x-app-layout>

    <div class=" flex justify-center items-center mt-8">
        <div class="lg:w-2/5 md:w-1/2 w-2/3">
            <form method="POST" action="{{ route('user.store') }}" class="bg-white p-10 rounded-lg shadow-lg min-w-full">
                @csrf
                <h1 class="text-center text-2xl mb-6 text-gray-600 font-bold font-sans">Create User</h1>
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

                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="email">Email</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="text" name="email" id="email"  />
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="password">Password</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="password" name="password" id="password"  />
                </div>
                <div>
                    <label class="text-gray-800 font-semibold block my-3 text-md" for="password_confirmation">Password Confirmation</label>
                    <input class="w-full bg-gray-100 px-4 py-2 rounded-lg focus:outline-none" type="password" name="password_confirmation" id="password_confirmation"  />
                </div>
                <button type="submit" class="w-full mt-6 mb-3 bg-blue-500  hover:bg-blue-600 rounded-lg px-4 py-2 text-lg text-gray-800 tracking-wide font-semibold font-sans">Create account</button>
            </form>
        </div>
    </div>
    </x-app-layout>
