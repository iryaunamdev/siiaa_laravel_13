<?php

namespace App\Models\Traits;

trait TracksUserActions
{
    protected static function bootTracksUserActions(): void
    {
        static::creating(function ($model) {
            if (! auth()->check()) {
                return;
            }

            if ($model->isFillable('created_by') || array_key_exists('created_by', $model->getAttributes())) {
                if (empty($model->created_by)) {
                    $model->created_by = auth()->id();
                }
            }

            if ($model->isFillable('updated_by') || array_key_exists('updated_by', $model->getAttributes())) {
                if (empty($model->updated_by)) {
                    $model->updated_by = auth()->id();
                }
            }
        });

        static::updating(function ($model) {
            if (! auth()->check()) {
                return;
            }

            if ($model->isFillable('updated_by') || array_key_exists('updated_by', $model->getAttributes())) {
                $model->updated_by = auth()->id();
            }
        });
    }
}