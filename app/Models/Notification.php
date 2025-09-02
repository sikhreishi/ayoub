<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'sender_id', 'title_ar', 'body_ar', 'title_en', 'body_en', 'image', 'data', 'read'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public static function countUnreadMessages($userId)
    {
        return self::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    public static function getRoleImage($roles)
    {
        $roleImageMap = [
            'admin' => 'images/roles/admin.png',
            'manager' => 'images/roles/manager.png',
            'support' => 'images/roles/support.png',
            'editor' => 'images/roles/editor.png',
        ];

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (isset($roleImageMap[$role])) {
                    return $roleImageMap[$role];
                }
            }
        } else {
            if (isset($roleImageMap[$roles])) {
                return $roleImageMap[$roles];
            }
        }

        return $roleImageMap['admin'];
    }
}
