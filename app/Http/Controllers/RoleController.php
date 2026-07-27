<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('roles.index');
    }

    public function datatable(Request $request)
    {
        $columns = ['id', 'name', 'description'];
        $query = Role::query()->withCount('users');
        $recordsTotal = Role::count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($columns[$orderIndex] ?? 'name', $orderDir);

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function (Role $role) {
                $actions = '';

                if (auth()->user()->can('view', $role)) {
                    $actions .= '<a href="' . route('roles.show', $role) . '" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a> ';
                }

                if (auth()->user()->can('update', $role)) {
                    $actions .= '<a href="' . route('roles.edit', $role) . '" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a> ';
                }

                if (auth()->user()->can('delete', $role)) {
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $role->id . ', \'' . e($role->name) . '\')"><i class="fas fa-trash"></i></button>';
                }

                return [
                    'id' => $role->id,
                    'name' => '<span class="badge ' . ($role->name === 'admin' ? 'bg-danger' : 'bg-success') . '">' . e($role->name) . '</span>',
                    'description' => e($role->description ?? '-'),
                    'users' => '<span class="badge ' . ($role->users_count > 0 ? 'bg-info' : 'bg-secondary') . '">' . $role->users_count . ' Pengguna</span>',
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
        $permissionGroups = Role::AVAILABLE_PERMISSIONS;
        return view('roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $validated['permissions'] = $request->input('permissions', []);
        Role::create($validated);

        flash_success('Role berhasil dibuat!');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissionGroups = Role::AVAILABLE_PERMISSIONS;
        return view('roles.edit', compact('role', 'permissionGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $validated['permissions'] = $request->input('permissions', []);
        $role->update($validated);

        flash_success('Role berhasil diperbarui!');
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting admin role
        if ($role->name === 'admin') {
            flash_error('Tidak dapat menghapus role admin!');
            return redirect()->route('roles.index');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            flash_error('Tidak dapat menghapus role yang masih memiliki pengguna!');
            return redirect()->route('roles.index');
        }

        $role->delete();

        flash_success('Role berhasil dihapus!');
        return redirect()->route('roles.index');
    }
}
