<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use GrahamCampbell\Markdown\Facades\Markdown;

class CaseStudiesService
{
    public function getCaseStudies(): Collection
    {
        $directory = base_path() . "/public/cs";
        if (is_dir($directory)) {
            $publicFolders  = File::directories($directory);
            return collect($publicFolders)->map(function ($folder) {
                $folderName = $this->convertDirName(basename($folder));
                $files      = File::files($folder);
                return [
                    'name'  => $folderName,
                    'files' => $files,
                ];
            });
        }
        return collect([]);
    }

    public function getFileContent(string $file): string | null
    {
        $filePath = base_path() . "/public/" . base64_decode($file);
        if (File::exists($filePath)) {
            $extension = File::extension($filePath);
            $content   = File::get($filePath);
            return ($extension === 'md') ? Markdown::convertToHtml($content) : $content;
        }
        return null;
    }

    private function convertDirName(string $name): string
    {
        return match ($name)
        {
            "0presentation"       => "📝️ Presentation of this project ##DONE##",
            "0setup"              => "⚙️ Prod env setup AKA Adilia! ##DONE##",
            "1basic-setup"        => "💻 Local env setup AKA Ready to Dev! ##DONE##",
            "2.1ci-cd"            => "🌤️ CI/CD with jenkins ##STARTED_NOT_DONE##",
            "2.2git-rule"         => "🧩 GitHub protection rule for master branch ##DONE##",
            "2.3wms"              => "📩 Website message service ##DONE##",
            "2.4gc-bucket"        => "☁️ Do backups to a bucket using google cloud ##DONE##",
            "3hide-routes-cookie" => "🙈 Hide routes in production ##DONE##",
            "4maintenance-mode"   => "🏗️ Site down in maintenance mode accessible only for devs ##DONE##",
            "5env_vars_without"   => "🙈 Hide env vars ##STARTED_NOT_DONE##",
            "6webpack-mix"        => "🧑‍💻 Client browser cache - how to handle ##STARTED_NOT_DONE##",
            "7rate-limit"         => "👮‍♀️Requests limit per route - rate limit security ##STARTED_NOT_DONE##",
            "8logging"            => "🗂️ Laravel log files ##STARTED_NOT_DONE##",
            "9.0error-pages"      => "📄 Laravel custom error views ##STARTED_NOT_DONE##",
            "9.0.1redis"          => "💿 Redis implementation ##STARTED_NOT_DONE##",
            "9.1swagger"          => "🕹️ Swagger implementation ##STARTED_NOT_DONE##",
            "9.2reboot-cron"      => "⚙️ Some jobs in prod ##STARTED_NOT_DONE##",
            "9.3unit-tests"       => "🧪 Phpunit tests ##STARTED_NOT_DONE##",
            "9.4es"               => "🔎 Elasticsearch implementation ##STARTED_NOT_DONE##",
            "9.5apc"              => "💿 APCu implementation ##NOT_STARTED##",
            "9.5caches"           => "💿 Backend cache system implementation for load balanced with multiple frontends ##NOT_STARTED##",
            "9.6auth2"            => "🔑 Implementation of authentication  with Sanctum ( Token / Refresh token management ) ##STARTED_NOT_DONE##",
            "9.7areas_for_admins" => "🔐 Special routes only accessible for some users ##STARTED_NOT_DONE##",
            "9.8dashboard"        => "🔐 Dashboard with the system status accessible only for admins ##STARTED_NOT_DONE##",
            default => $name,
        };
    }
}
