<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Throwable;

/**
 * Central write path for the audit trail. Text is trimmed to the column sizes, and a
 * logging failure is reported to the application log instead of aborting the admin
 * action that has already succeeded.
 */
class ActivityLogger
{
    public static function log(string $action, string $entityType, ?int $entityId, string $description, array $metadata = []): void
    {
        try {
            AdminActivityLog::create([
                'user_id' => Auth::id(),
                'action' => Str::limit($action, 250, ''),
                'entity_type' => Str::limit($entityType, 250, ''),
                'entity_id' => $entityId,
                'description' => Str::limit($description, 250, '...'),
                'metadata' => $metadata ?: null,
                'ip_address' => Request::ip(),
                'user_agent' => Str::limit((string) Request::userAgent(), 250, ''),
            ]);
        } catch (Throwable $e) {
            Log::error('Could not write the admin activity log', [
                'action' => $action,
                'entity_type' => $entityType,
                'exception' => get_class($e),
            ]);
        }
    }
}