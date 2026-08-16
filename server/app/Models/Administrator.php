<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Administrator extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'administrators';

    protected $primaryKey = 'Administrator_ID';

    public $timestamps = false;

    protected $fillable = [
        'Email',
        'Name',
        'Role',
        'Phone',
        'Password',
        'Image',
    ];

    protected $hidden = [
        'Password',
    ];

    public function getAuthPasswordName(): string
    {
        return 'Password';
    }

    public function getAuthPassword(): string
    {
        return $this->Password;
    }
}
