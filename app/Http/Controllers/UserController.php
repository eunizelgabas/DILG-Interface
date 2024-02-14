<?php

namespace App\Http\Controllers;

use App\Events\UserLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function apiIndex(Request $request)
{
    // Authenticate the user
    $user = Auth::user();

    if ($user) {
        return response()->json($user);
    } else {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}

    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|confirmed|string|min:6',
            'role'      => 'required'
        ]);

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
                'password' => 'nullable|string|min:8|confirmed', // Add password validation
            ]);

            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            // Update other fields as needed

            if (isset($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            return response()->json($user);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
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
}
