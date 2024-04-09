<?php

namespace public;

use App\Console\Commands\Messages\Backups\MessageBackupToCloud;
use Mockery;
use Tests\TestCase;

class MessageBackupToCloudTest extends TestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testCreatePathIfNotExists()
    {
        // Arrange
        $testPath = __DIR__ . '/test_dir'; // Adjust this path according to your needs
        $command = new MessageBackupToCloud();

        // Act
        // Ensure the directory doesn't exist before calling the method
        if (file_exists($testPath)) {
            $command->unlinkRecursive($testPath);
            rmdir($testPath); // Remove directory if it already exists
        }

        // Call the method
        $command->createPathIfNotExists($testPath);

        // Assert
        // Check if directory exists after calling the method
        $this->assertTrue(file_exists($testPath));
        $this->assertTrue(is_dir($testPath));
        //$this->assertEquals(0775, fileperms($testPath) & 0777); // Check directory permissions
    }

    public function testWriteDataToFile()
    {
        // Arrange
        $testData = ['test' => 'data']; // Test data to write
        $testPath = __DIR__ . '/test_dir'; // Test directory path
        $command  = new MessageBackupToCloud();

        // Act
        // Call the method
        $localFileSize = $command->writeDataToFile($testData, $testPath);

        // Assert
        // Check if file was created
        $this->assertFileExists($testPath . '/tmp_file.json');

        // Check if file size matches the size of the data written
        $this->assertEquals(strlen(json_encode($testData)), $localFileSize);

        $command->deleteTmpFile($testPath);

        // Check if file was deleted
        $this->assertFileDoesNotExist($testPath . '/tmp_file.json');
    }

    public function testDeleteTmpFile()
    {
        // Arrange
        $testPath = __DIR__ . '/test_dir'; // Caminho do diretório de teste
        $testFile = $testPath . '/tmp_file.json'; // Caminho do arquivo temporário
        file_put_contents($testFile, 'Test data'); // Cria um arquivo temporário

        $command = new MessageBackupToCloud();

        // Act
        $command->deleteTmpFile($testPath);

        // Assert
        $this->assertFileDoesNotExist($testFile); // Verifica se o arquivo foi excluído
    }



   /* public function testShouldSkipBackup()
    {
        // Mock do objeto de backup mais recente
        $latestBackupObjectMock = $this->getMockBuilder(\Google\Cloud\Storage\StorageObject::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configurando o mock para retornar o tamanho do último backup
        $latestBackupObjectMock->method('info')->willReturn(['size' => 1000]);

        // Mock do objeto do bucket
        $bucketMock = $this->getMockBuilder(\Google\Cloud\Storage\Bucket::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Configurando o mock para retornar o objeto de backup mais recente
        $bucketMock->method('object')->willReturn($latestBackupObjectMock);

        // Chamando a função shouldSkipBackup
        $messageBackupToCloud = new MessageBackupToCloud();
        $shouldSkip = $messageBackupToCloud->shouldSkipBackup(500, $bucketMock);

        // Verificando se o backup deve ser ignorado
        $this->assertTrue($shouldSkip);
    }

*/

    /*
        public function testPerformLocalBackups()
        {
            // Arrange
            $data = ['test' => 'data'];
            $path = __DIR__ . '/test_dir/';

            // Mocking env helper function
            $envMock = Mockery::mock('overload:Illuminate\Support\Facades\Env');
            $envMock->shouldReceive('get')->with('GC_HOST_FILE')->andReturn('testfile.json');

            // Mocking the file_put_contents function
            $filePutContentsMock = Mockery::mock('alias:file_put_contents');
            $filePutContentsMock->shouldReceive('file_put_contents')->twice();

            $command = new MessageBackupToCloud();

            // Act
            $command->performLocalBackups($data, $path);

            // Assert
            $this->assertFileExists($path . 'testfile.json');
            $this->assertFileExists($path . 'messages-backup-' . date("Y_m_d_H_i_s") . '.json');

            // Clean up
            File::delete($path . 'testfile.json');
            File::delete($path . 'messages-backup-' . date("Y_m_d_H_i_s") . '.json');
        } */

}
