<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\Response;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';

    public function Question() {
        return $this->belongsTo(Question::class);
    }

    public function Response() {
        return $this->belongsTo(Response::class);
    }
}
