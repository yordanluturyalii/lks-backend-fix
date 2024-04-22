<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Form;

class AllowedDomain extends Model
{
    use HasFactory;
    protected $table = 'allowed_domains';

    protected $guarded = ['id'];
    protected $fillable = [
        'form_id',
        'domain'
    ];
    protected $casts = [
        'domain' => 'array'
    ];

    public function Form() {
        return $this->belongsTo(Form::class, 'id', 'form_id');
    }


}
