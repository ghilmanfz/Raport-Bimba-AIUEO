<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$noNip = App\Models\Teacher::whereNull('nip')->orWhere('nip', '')->get();
echo "Teachers tanpa NIP: " . $noNip->count() . PHP_EOL;
foreach ($noNip as $t) {
    $nip = App\Models\Teacher::generateNip();
    $t->update(['nip' => $nip]);
    echo "  {$t->user->name} -> {$nip}" . PHP_EOL;
}
echo "Done!" . PHP_EOL;
