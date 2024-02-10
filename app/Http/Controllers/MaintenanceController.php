<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function deactivate(): string
    {
        Artisan::call('down');
        return 'Site is deactivated. Maintenance mode activated. Users will <strong>NOT</strong> be able to access the site.';
    }

    public function activate(): string
    {
        Artisan::call('up');
        return 'Site is activated. Maintenance mode activated. Users will be able to access the site.';
    }

}
