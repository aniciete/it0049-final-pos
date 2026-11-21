<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserRequest;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class UserController extends Controller
{
    /**
     * Display the staff user accounts directory.
     */
    public function index(): View
    {
        $users = User::query()
            ->withCount('sales')
            ->orderBy('id')
            ->get();

        return view('users.index', [
            'title' => 'Staff Accounts | Bright-Market POS',
            'activePage' => 'users',
            'users' => $users,
        ]);
    }

    /**
     * Display the form for creating a new staff user account.
     */
    public function create(): View
    {
        return view('users.create', [
            'title' => 'New Staff Account | Bright-Market POS',
            'activePage' => 'users',
            'user' => new User(['role' => 'Staff Member']),
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Validate input, prepare any uploaded avatar, and persist a new staff user account.
     */
    public function store(SaveUserRequest $request, ImageUploadService $imageService): RedirectResponse
    {
        $validated = $request->validated();
        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $imageService->storeAvatar($request->file('avatar'));
        }

        $attributes = [
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'] ?? 'Staff Member',
            'avatar' => $avatarPath,
            'password' => Hash::make($validated['password']),
        ];

        try {
            User::query()->create($attributes);
        } catch (Throwable $e) {
            if ($avatarPath !== null) {
                $imageService->delete($avatarPath);
            }

            throw $e;
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account created successfully.');
    }

    /**
     * Display an individual staff user account profile and role details.
     */
    public function show(User $user): View
    {
        $user->loadCount('sales');

        return view('users.show', [
            'title' => $user->full_name.' | Staff | Bright-Market POS',
            'activePage' => 'users',
            'user' => $user,
        ]);
    }

    /**
     * Display the form pre-filled with an existing staff user's data and avatar preview.
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'title' => 'Edit Staff Account | Bright-Market POS',
            'activePage' => 'users',
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Validate input, process optional avatar replacement, and update the staff account.
     */
    public function update(
        SaveUserRequest $request,
        User $user,
        ImageUploadService $imageService,
    ): RedirectResponse {
        $validated = $request->validated();
        $oldAvatar = $user->avatar;
        $newAvatarPath = null;
        $removeRequested = $request->boolean('remove_avatar');

        if ($request->hasFile('avatar')) {
            $newAvatarPath = $imageService->storeAvatar($request->file('avatar'));
        }

        $avatarToPersist = $newAvatarPath ?? ($removeRequested ? null : $oldAvatar);

        $attributes = [
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'] ?? $user->role ?? 'Staff Member',
            'avatar' => $avatarToPersist,
        ];

        if (! empty($validated['password'])) {
            $attributes['password'] = Hash::make($validated['password']);
        }

        try {
            $user->update($attributes);
        } catch (Throwable $e) {
            if ($newAvatarPath !== null) {
                $imageService->delete($newAvatarPath);
            }

            throw $e;
        }

        if ($oldAvatar !== null && ($newAvatarPath !== null || $removeRequested)) {
            $imageService->delete($oldAvatar);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account updated successfully.');
    }

    /**
     * Remove a staff user account without recorded sales and clean up its avatar.
     */
    public function destroy(Request $request, User $user, ImageUploadService $imageService): RedirectResponse
    {
        if ($user->is($request->user())) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete the currently signed-in account.');
        }

        if ($user->sales()->exists()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Staff members with recorded sales cannot be deleted.');
        }

        $avatarPath = $user->avatar;
        $user->delete();

        if ($avatarPath !== null) {
            $imageService->delete($avatarPath);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account deleted successfully.');
    }
}
