<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CsvReader;
use App\Services\StockValidator;
use App\Repositories\StockRepository;

/**
 * Class ImportStockData
 * 
 * This command is responsible for importing stock data from a CSV file into the database.
 * It can be run in test mode, where it performs all operations except for the actual data insertion.
 */
class ImportStockData extends Command
{
    // Define the command signature and description
    protected $signature = 'import:stock {--test}';
    protected $description = 'Import stock data from CSV file';

    // Declare dependencies
    protected $csvReader;
    protected $stockValidator;
    protected $stockRepository;

    /**
     * ImportStockData constructor.
     * 
     * @param CsvReader $csvReader - Service for reading CSV files.
     * @param StockValidator $stockValidator - Service for validating stock data.
     * @param StockRepository $stockRepository - Repository for interacting with the stock database table.
     */
    public function __construct(CsvReader $csvReader, StockValidator $stockValidator, StockRepository $stockRepository)
    {
        parent::__construct();
        $this->csvReader = $csvReader;
        $this->stockValidator = $stockValidator;
        $this->stockRepository = $stockRepository;
    }

    /**
     * Execute the console command.
     * 
     * This method handles the command execution. It reads the CSV file, validates the data, and optionally inserts the data into the database.
     * It also reports the results of the import process, including any errors encountered.
     */
    public function handle()
    {
        // Check if the script is running in test mode
        $testMode = $this->option('test');
        $filePath = storage_path('app/public/stock.csv');

        // Attempt to read the CSV file
        try {
            $data = $this->csvReader->read($filePath);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        // Validate the read data
        $validationResults = $this->stockValidator->validate($data);
        $processed = $validationResults['processed'];
        $successful = $validationResults['successful'];
        $skipped = $validationResults['skipped'];
        $errors = $validationResults['errors'];
        $validatedData = $validationResults['validatedData'];

        // Insert data into the database if not in test mode
        if (!$testMode) {
            foreach ($validatedData as $row) {
                try {
                    $this->stockRepository->insert($row);
                } catch (\Exception $e) {
                    $errors[] = $row['product_code'] . ' - ' . $e->getMessage();
                    $skipped++;
                }
            }
        }

        // Report the results
        $this->info("Processed: $processed");
        $this->info("Successful: $successful");
        $this->info("Skipped: $skipped");

        if (count($errors)) {
            $this->error('Some records failed to import:');
            foreach ($errors as $error) {
                $this->error($error);
            }
        } else {
            $this->info('Import completed successfully.');
        }
    }
}
