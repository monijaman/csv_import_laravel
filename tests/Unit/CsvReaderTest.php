<?php 

namespace Tests\Unit;

use App\Services\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    private $csvReader;

    /**
     * Set up the test environment.
     * 
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->csvReader = new CsvReader();
    }

    /**
     * Test reading a valid CSV file.
     * 
     * This test creates a temporary CSV file with valid content, reads it using 
     * the CsvReader service, and asserts that the data is correctly read and 
     * formatted.
     * 
     * @test
     */
    public function it_reads_a_valid_csv_file()
    {
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_csv_');
        $csvContent = "Product Code,Product Name,Product Description,Cost in GBP,Stock,Discontinued\n"
                    . "P001,Sample Product,Description,12.34,100,No";

        // Ensure file was created
        if ($tempFile === false) {
            $this->fail('Failed to create a temporary file.');
        }

        // Write content to the file
        if (file_put_contents($tempFile, $csvContent) === false) {
            $this->fail('Failed to write to the temporary file.');
        }

        // Read the CSV file using the CsvReader service
        try {
            $data = $this->csvReader->read($tempFile);
        } catch (\Exception $e) {
            $this->fail('Failed to read the CSV file: ' . $e->getMessage());
        }

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        // Check if the first row contains the expected keys
        $this->assertArrayHasKey('Product Code', $data[0]);
        $this->assertArrayHasKey('Product Name', $data[0]);
        $this->assertArrayHasKey('Product Description', $data[0]);
        $this->assertArrayHasKey('Cost in GBP', $data[0]);
        $this->assertArrayHasKey('Stock', $data[0]);
        $this->assertArrayHasKey('Discontinued', $data[0]);

        // Clean up the temporary file
        unlink($tempFile);
    }

    /**
     * Test that an exception is thrown for an invalid CSV file.
     * 
     * This test creates a temporary file and deletes it to simulate an invalid 
     * or missing file, then attempts to read it using the CsvReader service, 
     * expecting an exception to be thrown.
     * 
     * @test
     */
    public function it_throws_exception_for_invalid_csv_file()
    {
        $this->expectException(\Exception::class);

        // Create a temporary file and delete it to simulate an invalid file
        $tempFile = tempnam(sys_get_temp_dir(), 'test_csv_');

        // Ensure file was created
        if ($tempFile === false) {
            $this->fail('Failed to create a temporary file.');
        }

        // Delete the file to simulate it being missing
        unlink($tempFile);

        // Try to read the non-existent file
        $this->csvReader->read($tempFile);
    }
}
