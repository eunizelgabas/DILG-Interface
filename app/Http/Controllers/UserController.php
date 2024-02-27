<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // $users = User::with('roles')->paginate(6);
        $search = $request->input('search');

        $users = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
         })->with('roles')->orderBy('created_at', 'desc')->paginate(6);

        return view('user.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $user = new User();
        return view('user.create', compact('roles', 'user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|confirmed|string|min:6',
            'avatar'     =>'nullable|file|mimes:jpeg,png,jpg,gif|max:10240',
            'role'      => 'required'
        ]);

        $fileName = null;
        if ($request->hasFile('avatar')) {
            $fileName = time() . "." . $request->avatar->extension();
            $request->avatar->move(public_path('avatars/'), $fileName);
            $data['avatar'] = $fileName;
        }

        // Hash the password before creating the user
        $data['password'] = bcrypt($data['password']);
        unset($data['role']);
         // $type = $data['type'];
        $role = $request->role;

        $user = User::create($data);

        $user->assignRole($role);
        // $user->assignRole($data['role']);
        // Assign roles to the user
        // $token = $user->createToken('Personal Access Token')->plainTextToken;
        // $response = ['user' => $user, 'token' => $token];
        // return response()->json($response, 200);

        $log_entry = Auth::user()->name ." created a  user " . $user->name;
        event(new UserLog($log_entry));

        return redirect('/users')->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('user.show', ['user' => $user]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user){
        // $user = User::with('roles')->find($user->id);
        $roles = Role::all();
        $user->find($user->id);
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check if the request is authenticated
        if (Auth::check()) {
            // Get the authenticated user
            $authenticatedUser = Auth::user();

            // Check if the authenticated user is authorized to update the user data
            if ($authenticatedUser->id !== $user->id) {
                // Return a forbidden response if the authenticated user is not authorized
                return response()->json(['error' => 'Forbidden - You are not authorized to update this user'], 403);
            }

            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'nullable|string|min:8|confirmed', // Add password validation
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
            ]);

            // Update user data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            // Update password if provided
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            if ($request->hasFile('avatar')) {
                // Store the new avatar and get its path
                $avatarPath = $request->file('avatar')->store('avatars', 'public');

                // Delete the old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Update the user's avatar path
                $user->avatar = $avatarPath;
            }


            // Save the changes
            $user->save();

            // Return the updated user data
            return response()->json($user);
        } else {
            // User is not authenticated, return an unauthorized response
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    public function updateNotApi(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed', // Add password validation
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update password if provided
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('avatar')) {
            // Store the new avatar and get its path
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Delete the old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Update the user's avatar path
            $user->avatar = $avatarPath;
        }

        // Save the changes
        $user->save();

        // Redirect back with success message
        return redirect('/users')->with('success', 'User details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deactivate(User $user){
        $user->update(['status' => 0]);
        $log_entry = Auth::user()->name ." updated  user " . $user->name .  " status to deactivate ";
        event(new UserLog($log_entry));

        return redirect()->route('user.show', ['user' => $user->id])->with('success', 'User activated successfully');
    }

    public function activate(User $user){
        abort_if(!$user, 404); // Add this line to check if the user exists
        $user->update(['status' => 1]);

        $log_entry = Auth::user()->name ." updated  user " . $user->name .  " status to activate ";
        event(new UserLog($log_entry));


        return redirect()->route('user.show', ['user' => $user->id])->with('success', 'User activated successfully');
    }

    public function login(Request $request) {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $request->validate($rules);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function validateToken(Request $request)
        {
            // Check if the request contains a valid authorization header
            if (!$request->header('Authorization')) {
                return response()->json(['message' => 'Authorization header is missing'], 401);
            }

            // Extract the token from the authorization header
            $token = str_replace('Bearer ', '', $request->header('Authorization'));

            // Perform token validation logic here
            // This could involve verifying the token against your authentication provider,
            // checking token expiration, etc.

            // For example, if you're using Laravel Sanctum for token-based authentication:
            if (auth()->guard('api')->check()) {
                // Token is valid
                return response()->json(['message' => 'Token is valid'], 200);
            } else {
                // Token is invalid
                return response()->json(['message' => 'Token is invalid'], 401);
            }
        }


        public function getUser(Request $request)
        {
            // Retrieve the authenticated user based on the provided token
            $user = $request->user();

            // Return the user's information
            return response()->json(['user' => $user], 200);
        }
}
