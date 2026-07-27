<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    public function datatable(Request $request)
    {
        $columns = ['id', 'name', 'email', 'role', 'status'];
        $query = User::query();
        $recordsTotal = $query->count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($columns[$orderIndex] ?? 'name', $orderDir);

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function (User $user) {
                $actions = '';

                if (auth()->user()->can('view', $user)) {
                    $actions .= '<a href="' . route('users.show', $user) . '" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a> ';
                }

                if (auth()->user()->can('update', $user)) {
                    $actions .= '<a href="' . route('users.edit', $user) . '" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a> ';
                }

                if (auth()->user()->can('delete', $user)) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $user->id . ', \'' . e($user->name) . '\')"><i class="fas fa-trash"></i></button>';
                }

                $statusClass = $user->status === User::STATUS_ACTIVE ? 'bg-success' : 'bg-secondary';
                $statusLabel = $user->status === User::STATUS_ACTIVE ? 'Active' : 'Inactive';
                $toggleLabel = $user->status === User::STATUS_ACTIVE ? 'Nonaktifkan' : 'Aktifkan';
                $toggleClass = $user->status === User::STATUS_ACTIVE ? 'btn-outline-secondary' : 'btn-outline-success';
                $status = '<span class="badge ' . $statusClass . '">' . $statusLabel . '</span>';

                if (auth()->user()->can('update', $user) && auth()->id() !== $user->id) {
                    $status .= '
                        <form action="' . route('users.status', $user) . '" method="POST" class="d-inline ms-2">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button class="btn btn-sm ' . $toggleClass . '">' . $toggleLabel . '</button>
                        </form>';
                }

                return [
                    'id' => $user->id,
                    'name' => '<div class="d-flex align-items-center"><div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">' . e(strtoupper(substr($user->name, 0, 1))) . '</div><span class="ms-2">' . e($user->name) . '</span></div>',
                    'email' => e($user->email),
                    'role' => '<span class="badge ' . ($user->role === 'admin' ? 'bg-danger' : 'bg-success') . '">' . e($user->role) . '</span>',
                    'status' => $status,
                    'aksi' => '<div class="text-end">' . $actions . '</div>',
                ];
            });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        ActivityLogger::log('users.created', $user, 'Menambahkan pengguna', ['after' => $user->only(['id', 'name', 'email', 'role', 'status'])]);

        flash_success('User berhasil dibuat!');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent users from editing their own role or password if they are not admins
        if ($user->id === auth()->id() && !$request->has('role')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|string|exists:roles,name',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
            unset($validated['password_confirmation']);
        }

        $before = $user->only(['id', 'name', 'email', 'role', 'status']);
        $user->update($validated);
        ActivityLogger::log('users.updated', $user, 'Memperbarui pengguna', ['before' => $before, 'after' => $user->fresh()->only(['id', 'name', 'email', 'role', 'status'])]);

        flash_success('User berhasil diperbarui!');
        return redirect()->route('users.index');
    }

    public function updateStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            flash_error('Tidak dapat mengubah status akun Anda sendiri!');
            return redirect()->route('users.index');
        }

        $before = $user->status;
        $user->update([
            'status' => $user->status === User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE,
        ]);
        ActivityLogger::log('users.status_updated', $user, 'Mengubah status akun pengguna', ['before' => $before, 'after' => $user->status]);

        flash_success('Status akun berhasil diperbarui!');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === auth()->id()) {
            flash_error('Tidak dapat menghapus akun Anda sendiri!');
            return redirect()->route('users.index');
        }

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            flash_error('Tidak dapat menghapus akun admin!');
            return redirect()->route('users.index');
        }

        $payload = $user->only(['id', 'name', 'email', 'role', 'status']);
        $user->delete();
        ActivityLogger::log('users.deleted', $user, 'Menghapus pengguna', ['before' => $payload]);

        flash_success('User berhasil dihapus!');
        return redirect()->route('users.index');
    }
}
