<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailUserNotfiction extends Model
{
    //
    protected $fillable = ['user_id','email_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function emailNotfiction()
    {
        return $this->belongsTo(EmailNotfiction::class, 'email_id');
    }
}
