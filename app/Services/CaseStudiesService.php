<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use GrahamCampbell\Markdown\Facades\Markdown;

class CaseStudiesService
{
    public function getCaseStudies()
    {
        $directory = base_path() . "/public/cs";
        if (is_dir($directory)) {
            $publicFolders = File::directories($directory);
            return collect($publicFolders)->map(function ($folder) {
                $folderName = $this->convertDirName(basename($folder));
                $files      = File::files($folder);
                return [
                    'name' => $folderName,
                    'files' => $files,
                ];
            });
        }
        return collect([]);
    }

    public function getFileContent($file)
    {
        $filePath = base_path() . "/public/" . base64_decode($file);
        if (File::exists($filePath)) {
            $extension = File::extension($filePath);
            $content   = File::get($filePath);
            return ($extension === 'md') ? Markdown::convertToHtml($content) : $content;
        }
        return null;
    }

    private function convertDirName($name): string
    {
        return match ($name)
        {
            "0setup"              => "⚙️ Prod env Project - complete infra setup",
            "1basic-setup"        => "💻 Local env setup AKA Ready to Dev! ##DONE##",
            "2.1ci-cd"            => "🌤️ Project CI/CD with jenkins",
            "2.2git-rule"         => "🧩 GitHub protection rule for master branch ##DONE##",
            "2.3wms"              => "📩 Website message service",
            "2.4gc-bucket"        => "☁️ Do backups to a bucket using google cloud",
            "3hide-routes-cookie" => "🙈 Hide routes in production ##DONE##",
            "4maintenance-mode"   => "🏗️ Site down in maintenance mode accessible only for devs",
            "5env_vars_without"   => "🙈 Hide env vars",
            "6webpack-mix"        => "🧑‍💻 Client browser cache - how to handlee",
            "7rate-limit"         => "👮‍♀️Requests limit per route - rate limit security",
            "8logging"            => "🗂️ Laravel log files",
            "9.0error-pages"      => "📄 Laravel custom error views",
            "9.0.1redis"          => "💿 Redis implementation",
            "9.1swagger"          => "🕹️ Swagger implementation",
            "9.2reboot-cron"      => "⚙️ Some jobs in prod",
            "9.3unit-tests"       => "🧪 Phpunit tests",
            "9.4es"               => "🔎 Elasticsearch implementation",
            default => $name,
        };
    }
}
