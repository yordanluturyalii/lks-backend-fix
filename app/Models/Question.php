<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Form;
use App\Models\Answer;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $casts = [
        'choices' => 'array'
    ];

    public function Form() {
        return $this->belongsTo(Form::class);
    }

    public function Answers() {
        return $this->hasMany(Answer::class);
    }
}
