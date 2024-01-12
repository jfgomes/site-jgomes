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

    private function convertDirName($name)
    {
        switch ($name) {
            case "wms":
                return "Website message service";
            case "test":
                return "Cache implementation";
            case "test2":
                return "Elasticsearch implementation";
            case "basic-setup":
                return "Project basic setup";
            case "setup":
                return "Project complete infra setup";
            default:
                return $name;
        }
    }
}
