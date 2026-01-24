<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'user_id',
        'action',
        'type',
        'event',
        'model',
        'model_uuid',
        'item_id',
        'exception_class',
        'exception_code',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
