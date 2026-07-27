<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'permissions',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'permissions' => 'array',
    ];

    public const AVAILABLE_PERMISSIONS = [
        'Dashboard' => [
            'dashboard.view' => 'Lihat dashboard',
        ],
        'Barang' => [
            'barang.view' => 'Lihat barang',
            'barang.create' => 'Tambah barang',
            'barang.update' => 'Edit barang',
            'barang.delete' => 'Hapus barang',
        ],
        'Barang Masuk' => [
            'barang_masuk.view' => 'Lihat barang masuk',
            'barang_masuk.create' => 'Tambah barang masuk',
        ],
        'Barang Keluar' => [
            'barang_keluar.view' => 'Lihat barang keluar',
            'barang_keluar.create' => 'Ajukan barang keluar',
            'barang_keluar.approve' => 'Setujui barang keluar',
            'barang_keluar.reject' => 'Tolak barang keluar',
        ],
        'Pengguna' => [
            'users.view' => 'Lihat pengguna',
            'users.create' => 'Tambah pengguna',
            'users.update' => 'Edit pengguna',
            'users.delete' => 'Hapus pengguna',
        ],
        'Role Akses' => [
            'roles.view' => 'Lihat role akses',
            'roles.create' => 'Tambah role akses',
            'roles.update' => 'Edit role akses',
            'roles.delete' => 'Hapus role akses',
        ],
        'Log Activity' => [
            'activity_logs.view' => 'Lihat log activity',
        ],
    ];

    /**
     * Get all users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Check if role has permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }

    /**
     * Check if user has this role.
     */
    public function hasUser(int $userId): bool
    {
        return $this->users()->where('id', $userId)->exists();
    }
}
