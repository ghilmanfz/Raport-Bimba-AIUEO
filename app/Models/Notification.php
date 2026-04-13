<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'icon', 'title', 'message', 'link', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public static function send($userId, $title, $message, $type = 'info', $icon = 'lucide:bell', $link = null)
    {
        return static::create([
            'user_id' => $userId,
            'type'    => $type,
            'icon'    => $icon,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }

    public static function notifyAdmins($title, $message, $type = 'info', $icon = 'lucide:bell', $link = null)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            static::send($admin->id, $title, $message, $type, $icon, $link);
        }
    }
}
