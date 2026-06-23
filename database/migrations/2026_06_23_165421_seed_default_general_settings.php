<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'site_title', 'value' => 'Excellency'],
            ['key' => 'site_logo', 'value' => null],
            ['key' => 'site_favicon', 'value' => null],
            ['key' => 'institute_name', 'value' => ''],
            ['key' => 'institute_address', 'value' => ''],
            ['key' => 'institute_phone', 'value' => ''],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'site_title', 'site_logo', 'site_favicon',
            'institute_name', 'institute_address', 'institute_phone',
        ])->delete();
    }
};
