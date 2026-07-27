<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, ?Model $entity = null, ?string $description = null, array $properties = [], ?Request $request = null): void
    {
        $request ??= request();
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'channel' => $request->is('api/*') ? 'api' : 'web',
            'action' => $action,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }
}
