<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Generic observer that records create/update/delete activity on the models
 * it's attached to, capturing who did it and when (audit trail).
 */
class ActivityObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model);
    }

    public function updated(Model $model): void
    {
        $this->log('updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model);
    }

    protected function log(string $action, Model $model): void
    {
        try {
            if (! Schema::hasTable('activity_logs')) {
                return;
            }

            $user = auth()->user();

            ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'System',
                'action' => $action,
                'subject_type' => class_basename($model),
                'subject_id' => $model->getKey(),
                'subject_label' => $this->label($model),
                'ip' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the actual operation.
        }
    }

    protected function label(Model $model): string
    {
        foreach (['title', 'name', 'order_number', 'code', 'subject', 'email'] as $attr) {
            if (! empty($model->{$attr})) {
                return (string) $model->{$attr};
            }
        }

        return '#'.$model->getKey();
    }
}
