<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AccountStatus;
use App\Enums\Gender;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function index(Request $request): View
    {
        // Only fully-onboarded astrologers (those with a profile) live in the
        // Astrologers section. Incomplete astrologer signups stay visible here.
        $query = User::whereDoesntHave('astrologerProfile');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(15),
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        // A fully-onboarded astrologer is managed in the Astrologers section.
        if ($astrologer = $user->astrologerProfile) {
            return redirect()->route('admin.astrologers.show', $astrologer);
        }

        return view('admin.users.show', [
            'user' => $user->load(['wallet', 'astrologerProfile']),
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeManage($request->user(), $user);

        return view('admin.users.edit', [
            'user' => $user,
            'assignableRoles' => $this->assignableRoles($request->user()),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($request->user(), $user);

        $assignableRoles = $this->assignableRoles($request->user());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/', Rule::unique('users', 'mobile')->ignore($user->id)],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'preferred_language' => ['nullable', 'string', 'max:5'],
            'role' => ['required', Rule::in(array_keys($assignableRoles))],
            'account_status' => ['required', Rule::enum(AccountStatus::class)],
        ]);

        // Guard against an admin locking themselves out of their own role.
        if ($user->is($request->user()) && $validated['role'] !== $user->role->value) {
            return back()->withErrors(['role' => 'You cannot change your own role.'])->withInput();
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'gender' => ($validated['gender'] ?? null) ? Gender::from($validated['gender']) : null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'preferred_language' => $validated['preferred_language'] ?? 'en',
            'role' => UserRole::from($validated['role']),
            'account_status' => AccountStatus::from($validated['account_status']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'account_status' => ['required', 'in:active,suspended,deactivated'],
        ]);

        $user->update(['account_status' => AccountStatus::from($request->input('account_status'))]);

        return back()->with('success', 'User status updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403, 'You cannot delete your own account.');

        // Only a super admin may delete another admin or super admin.
        abort_if($user->isAdmin() && ! $request->user()->isSuperAdmin(), 403);

        if ($astrologer = $user->astrologerProfile) {
            $this->astrologerService->purgeFiles($astrologer);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    private function authorizeManage(User $actor, User $target): void
    {
        // Only a super admin may manage another admin or super admin.
        if ($target->isAdmin() && ! $actor->isSuperAdmin()) {
            abort(403);
        }
    }

    /**
     * @return array<string, string>
     */
    private function assignableRoles(User $actor): array
    {
        $roles = [
            UserRole::Customer->value => 'Customer',
            UserRole::Astrologer->value => 'Astrologer',
            UserRole::Sales->value => 'Sales',
        ];

        if ($actor->isSuperAdmin()) {
            $roles[UserRole::Admin->value] = 'Admin';
            $roles[UserRole::SuperAdmin->value] = 'Super Admin';
        }

        return $roles;
    }
}
