<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $users = User::with(['roles', 'stores'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('id', $role);
                });
            })
            ->when($request->store, function ($query, $store) {
                $query->whereHas('stores', function ($q) use ($store) {
                    $q->where('id', $store);
                });
            })
            ->when($request->active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->active);
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')
                ->store('profile-pictures', 'public');
        }

        $user = User::create($data);

        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        if (isset($data['stores'])) {
            $user->stores()->sync($data['stores']);
        }

        return new UserResource($user->load(['roles', 'stores']));
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['roles', 'stores']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')
                ->store('profile-pictures', 'public');
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $this->authorize('manageRoles', $user);
            $user->roles()->sync($data['roles']);
        }

        if (isset($data['stores'])) {
            $this->authorize('manageStores', $user);
            $user->stores()->sync($data['stores']);
        }

        return new UserResource($user->load(['roles', 'stores']));
    }

    public function destroy(User $user)
    {
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function loginHistory(User $user)
    {
        $this->authorize('view', $user);
        
        $logs = LoginLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($logs);
    }

    public function activate(User $user)
    {
        $this->authorize('update', $user);
        
        $user->update(['is_active' => true]);

        return new UserResource($user);
    }

    public function deactivate(User $user)
    {
        $this->authorize('update', $user);
        
        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'user' => ['Cannot deactivate an administrator account']
            ]);
        }

        $user->update(['is_active' => false]);

        return new UserResource($user);
    }

    public function updateRoles(Request $request, User $user)
    {
        $this->authorize('manageRoles', $user);
        
        $validated = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id']
        ]);

        $user->roles()->sync($validated['roles']);

        return new UserResource($user->load('roles'));
    }

    public function updateStores(Request $request, User $user)
    {
        $this->authorize('manageStores', $user);
        
        $validated = $request->validate([
            'stores' => ['required', 'array'],
            'stores.*' => ['exists:stores,id']
        ]);

        $user->stores()->sync($validated['stores']);

        return new UserResource($user->load('stores'));
    }

    public function enableTwoFactor(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20']
        ]);

        $user->update([
            'two_factor_enabled' => true,
            'phone' => $validated['phone']
        ]);

        return new UserResource($user);
    }

    public function disableTwoFactor(User $user)
    {
        $this->authorize('update', $user);

        $user->update(['two_factor_enabled' => false]);

        return new UserResource($user);
    }
}