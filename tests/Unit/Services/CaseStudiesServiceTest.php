<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CaseStudiesService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class CaseStudiesServiceTest extends TestCase
{
    protected CaseStudiesService $caseStudiesService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->caseStudiesService = new CaseStudiesService();
    }

    public function testGetCaseStudies()
    {
        // Simulate directory structure with test folders and files
        $fakeDirectories = [
            '/public/cs/folder1' => ['file1.txt', 'file2.md'],
            '/public/cs/folder2' => ['file3.txt'],
        ];

        // Mock File facade
        File::shouldReceive('directories')
            ->with(base_path() . "/public/cs")
            ->andReturn(array_keys($fakeDirectories));

        File::shouldReceive('files')
            ->andReturnUsing(function ($directory) use ($fakeDirectories) {
                return $fakeDirectories[$directory];
            });

        // Call the method to get case studies
        $caseStudies = $this->caseStudiesService->getCaseStudies();

        // Assert that the returned value is a collection
        $this->assertInstanceOf(Collection::class, $caseStudies);

        // Assert that the case studies contain the expected data
        $this->assertCount(count($fakeDirectories), $caseStudies);
        $this->assertEquals('folder1', $caseStudies[0]['name']);
        $this->assertCount(2, $caseStudies[0]['files']);
        $this->assertEquals('folder2', $caseStudies[1]['name']);
        $this->assertCount(1, $caseStudies[1]['files']);
    }

    public function testGetFileContent()
    {
        // Mock File facade to return a fake file content
        $fileContent = "This is a test file content.";
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('extension')->andReturn('txt'); // Adicionando expectativa para o método extension
        File::shouldReceive('get')->andReturn($fileContent);

        // Call the method to get file content
        $content = $this->caseStudiesService->getFileContent(base64_encode('path/to/file.txt'));

        // Assert that the returned content matches the expected value
        $this->assertEquals($fileContent, $content);
    }
}
