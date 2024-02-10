<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function activate(): string
    {
        Artisan::call('down');
        return 'Maintenance mode activated.';
    }

    public function deactivate(): string
    {
        Artisan::call('up');
        return 'Maintenance mode activated.';
    }

}
