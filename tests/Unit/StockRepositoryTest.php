<?php

namespace Tests\Unit;

use App\Repositories\StockRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $stockRepository;

    /**
     * Set up the test environment.
     * 
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->stockRepository = new StockRepository();
    }

    /**
     * Test inserting data into the stock table.
     * 
     * This test ensures that the data is correctly inserted into the stock table 
     * by using the StockRepository's insert method and then verifying the data 
     * in the database.
     * 
     * @test
     */
    public function it_inserts_data_into_stock_table()
    {
        // Sample data to insert
        $data = [
            'product_code' => 'P003',
            'product_name' => 'New Product',
            'product_description' => 'Description',
            'price' => 50.00,
            'stock_level' => 20,
            'discontinued' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert the data into the stock table
        $this->stockRepository->insert($data);

        // Assert that the data exists in the database
        $this->assertDatabaseHas('stock', ['product_code' => 'P003']);
    }
}
