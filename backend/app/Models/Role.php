<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    public function isCommercial()
    {
        return $this->name === 'commercial';
    }

    public function isMagasinier()
    {
        return $this->name === 'magasinier';
    }
}