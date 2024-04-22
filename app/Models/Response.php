<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Form;
use App\Models\Answer;

class Response extends Model
{
    use HasFactory;
    protected $table = 'responses';

    public function Form() {
        return $this->belongsTo(Form::class, 'id', 'form_id');
    }

    public function User() {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function Answers() {
        return $this->hasMany(Answer::class);
    }
}
