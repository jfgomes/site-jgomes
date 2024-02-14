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
            "1basic-setup"        => "💻 Local env setup AKA Ready to Dev! ##DONE##",
            "2git-rule"           => "🧩 GitHub protection rule for master branch ##DONE##",
            "3hide-routes-cookie" => "🙈 Hide routes in production ##DONE##",
            "4maintenance-mode"   => "🏗️ Site down in maintenance mode accessible only for devs",
            "5env_vars_without"   => "🙈 Hide env vars",
            "6webpack-mix"        => "🧑‍💻 Client browser cache - how to handlee",
            "7rate-limit"         => "👮‍♀️Requests limit per route - rate limit security",
            "8logging"            => "🗂️ Laravel log files",
            "9error-pages"        => "📄 Laravel custom error views",
            "wms"          => "Website message service",
            "test"         => "Cache implementation",
            "test2"        => "Elasticsearch implementation",

            "setup"        => "Project complete infra setup",
            "cicd"         => "Project ci/cd with jenkins",

            default => $name,
        };
    }
}
