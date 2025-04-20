<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'bet_menus';
    protected $fillable = [
        'title',
        'text',
        'banner',
        'image',
    ];
}

