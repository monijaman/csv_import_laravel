<?php 

namespace App\Services;

/**
 * Class CsvReader
 * 
 * This service handles reading and parsing CSV files. It reads the content of a CSV file and returns it as an array of associative arrays.
 */
class CsvReader
{
    /**
     * @var array|null The header row of the CSV file.
     */
    protected $header;

    /**
     * Read the CSV file and return its contents as an array.
     * 
     * @param string $filePath - The path to the CSV file to be read.
     * @return array - An array of associative arrays, where each array represents a row in the CSV file.
     * @throws \Exception - Throws an exception if the CSV file cannot be opened.
     */
    public function read($filePath)
    {
        // Open the CSV file for reading
        $handle = fopen($filePath, 'r');

        // Throw an exception if the file cannot be opened
        if (!$handle) {
            throw new \Exception('Cannot open the CSV file.');
        }

        // Check and skip BOM (Byte Order Mark) if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Read the header row from the CSV file
        $this->header = fgetcsv($handle, 1000, ',');
        $rows = [];

        // Read each subsequent row from the CSV file
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Check if the number of columns in the row matches the header
            if (count($this->header) !== count($row)) {
                continue; // Skip rows with column mismatch
            }

            // Combine header and row data into an associative array
            $rows[] = array_combine($this->header, $row);
        }

        // Close the file handle
        fclose($handle);

        // Return the array of rows
        return $rows;
    }
}
