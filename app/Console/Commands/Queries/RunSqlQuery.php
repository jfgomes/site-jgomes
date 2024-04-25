<?php

namespace App\Console\Commands\Queries;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSqlQuery extends Command
{
    protected $signature = 'query:run {file}';

    protected $description = 'Executes an SQL query from a file';

    public function handle(): void
    {
        $file = $this->argument('file');

        // Check if the file exists
        if (!File::exists($file)) {
            $this->error('The specified file does not exist.');
            return;
        }

        // Read the SQL query from the file
        $query = File::get($file);

        // Execute the SQL query
        try {
            DB::statement($query);
            $this->info('SQL query executed successfully.');
        } catch (\Exception $e) {
            $this->error('An error occurred while executing the SQL query: ' . $e->getMessage());
        }
    }
}
