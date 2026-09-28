<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use App\ValueObjects\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->paginate(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request, UserService $userService): RedirectResponse
    {
        $userInfo = $request->toUserDto();
        $user = $userService->storeUser($userInfo);

        return $this->redirectWithPassword("User {$user->name} created.", $userInfo->password);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserService $userService
    ): RedirectResponse {
        $userInfo = $request->toUserDto();
        $generatePassword = $request->boolean('generate_password');
        $user = $userService->updateUser($user, $userInfo, $generatePassword);

        $user->save();

        if ($user->is($request->user())) {
            Auth::setUser($user);
        }

        $status = "User {$user->name} updated.";
        return $generatePassword
            ? $this->redirectWithPassword($status, $userInfo->password)
            : redirect()
                ->route('admin.users.index')
                ->with('status', $status);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User {$user->name} deleted.");
    }

    private function redirectWithPassword(string $status, Password $generatedPassword): RedirectResponse
    {
        return redirect()
            ->route('admin.users.index')
            ->with('status', $status)
            ->with('generated_password', $generatedPassword->value);
    }
}
