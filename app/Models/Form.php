<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Question;
use App\Models\Response;
use App\Models\AllowedDomain;

class Form extends Model
{
    use HasFactory;
    protected $table = 'forms';
    protected $guarded = ['id'];

    public function User() {
        return $this->belongsTo(User::class);
    }

    public function Questions() {
        return $this->hasMany(Question::class);
    }

    public function Responses() {
        return $this->hasMany(Response::class, 'id', 'form_id');
    }

    public function AllowedDomain() {
        return $this->hasOne(AllowedDomain::class);
    }
}
