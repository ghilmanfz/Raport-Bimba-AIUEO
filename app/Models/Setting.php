<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function normalizeWhatsapp(?string $number): string
    {
        $number = preg_replace('/\D+/', '', $number ?? '');

        return str_starts_with($number, '0') ? '62'.substr($number, 1) : $number;
    }

    public static function supportWhatsappUrl(string $message = 'Halo Admin BiMBA, saya butuh bantuan terkait sistem E-Rapor.'): ?string
    {
        $number = static::normalizeWhatsapp(static::get('support_whatsapp', ''));

        return preg_match('/^[1-9][0-9]{7,14}$/', $number)
            ? 'https://wa.me/'.$number.'?text='.rawurlencode($message)
            : null;
    }
}
