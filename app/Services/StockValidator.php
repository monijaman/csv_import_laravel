<?php

namespace App\Services;
use Carbon\Carbon;
use App\Repositories\StockRepository;

/**
 * Class StockValidator
 * 
 * This service validates and prepares stock data for insertion into the database. It applies business rules to filter and transform the data.
 */
class StockValidator
{
    /**
     * @var string The current date in 'Y-m-d' format.
     */
    protected $currentDate;
    protected $stockRepository;

    /**
     * StockValidator constructor.
     * 
     * Initializes the current date for use in validation and data preparation.
     */
    public function __construct(StockRepository $stockRepository)
    {
        $this->currentDate = Carbon::now()->toDateString();
        $this->stockRepository = $stockRepository;

    }

    /**
     * Validate and process the stock data.
     * 
     * This method applies validation rules to the data and prepares it for database insertion.
     * 
     * @param array $data - An array of associative arrays, where each array represents a row of CSV data.
     * @return array - An array containing the counts of processed, successful, and skipped records, an array of errors, and the validated data.
     */
    public function validate(array $data)
    {
        $processed = 0;
        $successful = 0;
        $skipped = 0;
        $errors = [];
        $validatedData = [];

        foreach ($data as $row) {
            $processed++;

            // Ensure Discontinued column has a default value if missing
            if (!isset($row['Discontinued'])) {
                $row['Discontinued'] = null;
            }

            // Validation rules
            // Skip records with cost < 5 and stock < 10
            if (floatval($row['Cost in GBP']) < 5 && intval($row['Stock']) < 10) {
                $skipped++;
                continue;
            }
            // Skip records with cost > 1000
            if (floatval($row['Cost in GBP']) > 1000) {
                $skipped++;
                continue;
            }


            // Check for duplicates. uncomment below if you want duplicate check

            /*
            if ($this->stockRepository->exists($row['Product Code'])) {
                $errors[] = "Duplicate product code: " . $row['Product Code'];
            $skipped++;
                continue;
            } */

            // Set the Discontinued date if the record is marked as discontinued
            $row['Discontinued'] = strtolower($row['Discontinued']) === 'yes' ? $this->currentDate : null;

            // Prepare data for insertion into the database
            $record = [
                'product_code' => $row['Product Code'],
                'product_name' => $row['Product Name'],
                'product_description' => $row['Product Description'],
                'price' => floatval($row['Cost in GBP']),
                'stock_level' => intval($row['Stock']),
                'discontinued' => $row['Discontinued'],
                'created_at' => $this->currentDate,
                'updated_at' => $this->currentDate,
            ];

            // Add the processed record to the validated data array
            $validatedData[] = $record;
            $successful++;
        }

        // Return the results of the validation process
        return compact('processed', 'successful', 'skipped', 'errors', 'validatedData');
    }
}
