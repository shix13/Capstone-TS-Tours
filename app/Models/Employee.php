<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;


class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard='employee';
    
    protected $table = 'employees';
    protected $primaryKey = 'empID';
    protected $fillable = [
        'profile_img',
        'firstName',
        'lastName',
        'accountType',
        'mobileNum',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function rents()
    {
        return $this->belongsToMany(Rent::class, 'vehicles_assigned', 'empID');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'reserveID');
    }

    public function vehicleAssignments()
    {
        return $this->hasMany(VehicleAssigned::class, 'empID');
    }
}
