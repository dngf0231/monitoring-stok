<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user) {
            return $user->isAdmin() ? true : null;
        });

        $abilities = [
            User::class => 'users',
            Role::class => 'roles',
            Barang::class => 'barang',
            BarangMasuk::class => 'barang_masuk',
            BarangKeluar::class => 'barang_keluar',
            ActivityLog::class => 'activity_logs',
        ];

        foreach ($abilities as $model => $prefix) {
            foreach (['view', 'create', 'update', 'delete', 'approve', 'reject'] as $action) {
                Gate::define($action, function (User $user) use ($prefix, $action) {
                    return $user->hasPermission($prefix . '.' . $action);
                });
            }
        }

        Gate::define('dashboard.view', function (User $user) {
            return $user->hasPermission('dashboard.view');
        });
    }
}
