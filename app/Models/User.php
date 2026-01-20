<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use App\Constants\UserType;
use App\Constants\UserStatus;
use App\Constants\UserEmailVerificationStatus;
use App\Constants\UserGender;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Contracts\OAuthenticatable;

/**
 * @method bool hasRole(string|array $roles)
 * @method bool hasAnyRole(string|array $roles)
 * @method bool hasAllRoles(string|array $roles)
 */
class User extends Authenticatable implements OAuthenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'user_type',
        'email_verification_status',
        'email_verified_at',
        'user_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Boot function for UUID auto-generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /* -----------------------------------------------------------------
     | Relationships
     |-----------------------------------------------------------------
     | Define relationships here (roles already via HasRoles)
     ----------------------------------------------------------------- */

    // Example: complaints relationship
    public function complaints()
    {
        return $this->hasMany(\App\Models\FuelStationComplaint::class, 'user_uuid', 'uuid');
    }

    /* -----------------------------------------------------------------
     | Scopes
     |----------------------------------------------------------------- */

    // User type scopes
    public function scopeAdministrators($query)
    {
        return $query->where('user_type', UserType::ADMIN);
    }

    public function scopeExecutives($query)
    {
        return $query->where('user_type', UserType::EXECUTIVE);
    }

    public function scopePumpManagers($query)
    {
        return $query->where('user_type', UserType::FUEL_STATION_MANAGER);
    }

    // Status scopes
    public function scopeActive($query)
    {
        return $query->where('user_status', UserStatus::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('user_status', UserStatus::INACTIVE);
    }

    // Phone verification scopes
    public function scopePhoneVerified($query)
    {
        return $query->where('phone_verification_status', UserEmailVerificationStatus::VERIFIED);
    }

    public function scopePhoneUnverified($query)
    {
        return $query->where('phone_verification_status', UserEmailVerificationStatus::UNVERIFIED);
    }

    /* -----------------------------------------------------------------
     | Accessors / Computed Attributes
     |----------------------------------------------------------------- */

    // User Type
    public function getUserTypeLabelAttribute(): string
    {
        return UserType::label($this->user_type);
    }

    // User Status
    public function getUserStatusLabelAttribute(): string
    {
        return UserStatus::label($this->user_status);
    }

    public function getUserStatusColorAttribute(): string
    {
        return UserStatus::colors()[$this->user_status] ?? 'secondary';
    }

    public function getUserStatusIconAttribute(): string
    {
        return UserStatus::icons()[$this->user_status] ?? 'ki-question';
    }

    // Gender
    public function getGenderLabelAttribute(): string
    {
        return UserGender::label($this->gender);
    }

    public function getGenderColorAttribute(): string
    {
        return UserGender::color($this->gender);
    }

    public function getGenderIconAttribute(): string
    {
        return UserGender::icon($this->gender);
    }

    // Email verification status
    public function getEmailVerificationStatusLabelAttribute(): string
    {
        return UserEmailVerificationStatus::label($this->email_verification_status);
    }

    public function getEmailVerificationStatusColorAttribute(): string
    {
        return UserEmailVerificationStatus::colors()[$this->email_verification_status] ?? 'warning';
    }

    public function getEmailVerificationStatusIconAttribute(): string
    {
        return UserEmailVerificationStatus::icons()[$this->email_verification_status] ?? 'ki-shield-cross';
    }
}
