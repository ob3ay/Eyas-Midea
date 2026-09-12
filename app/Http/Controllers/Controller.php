<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * تسجيل العملية في Audit Log
     */
    protected function audit(
        string $actionType,
        string $entityType,
        int $entityId,
        ?array $oldValue = null,
        ?array $newValue = null
    ): AuditLog {
        return AuditLog::record(
            $actionType,
            $entityType,
            $entityId,
            $oldValue,
            $newValue
        );
    }
}

