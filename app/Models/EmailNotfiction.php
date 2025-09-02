<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailNotfiction extends Model
{
    protected $fillable = ['title', 'body'];

    public function emailUserNotfictions()
    {
        return $this->hasMany(EmailUserNotfiction::class, 'email_id');
    }






}
