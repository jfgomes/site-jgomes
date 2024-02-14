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
            "wms"         => "Website message service",
            "test"        => "Cache implementation",
            "test2"       => "Elasticsearch implementation",
            "basic-setup" => "💻 Local env setup AKA Ready to Dev!",
            "setup"       => "Project complete infra setup",
            "cicd"        => "Project ci/cd with jenkins",
            "git-rule"    => "🧩 GitHub protection rule for master branch",
            "hide-routes-cookie" => "🙈 Hide routes in production",
            default => $name,
        };
    }
}
