<?php

namespace App\Models;

use App\Models\Category;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'enable_notification'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function hasAvatar(): bool
    {
        return $this->avatar !== 'user.png';
    }

    public function adminlte_image()
    {
        if (! $this->hasAvatar()) {
            return asset('images/user.png');
        }

        return Storage::url($this->avatar);
    }

    public function sendPasswordResetNotification($token)
    {
        $url = env('RESET_PASSWORD_URL') . 'reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }
}
