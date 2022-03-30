<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_info';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
//        'id_user',
        'name',
        'lastname',
        'FullName',
        'username',
        'password',
        'MelliCode',
        'StudentCode',
        'student_tell',
        'BirthDay',
        'Reshte',
        'Paie',
        'Class',
        'Address',
        'studentMail',
        'gender',
        'picture',
        'fatherName',
        'fatherTell',
        'fatherJob',
        'fatherMelliCode',
        'fatherPassword',
        'fatherMail',
        'motherName',
        'motherLastName',
        'motherTell',
        'motherJob',
        'motherMelliCode',
        'motherPassword',
        'motherMail',
        'preRegistration',
        'deletedStatus',
        'user_type',
        'more_info',
        'manager_id',
        'background_type',
        'xp'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
//        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
