<?php
<?php

namespace Tests\Unit;

use App\Services\StockValidator;
use PHPUnit\Framework\TestCase;

class StockValidatorTest extends TestCase
{
    private $stockValidator;

    /**
     * Set up the test environment.
     * 
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->stockValidator = new StockValidator();
    }

    /**
     * Test that valid data is correctly validated and processed.
     * 
     * This test provides valid data to the StockValidator and checks that it 
     * processes the data correctly, marking it as successful and without errors.
     * 
     * @test
     */
    public function it_validates_and_processes_valid_data()
    {
        // Sample valid data
        $data = [
            [
                'Product Code' => 'P001',
                'Product Name' => 'Test Product',
                'Product Description' => 'Description',
                'Cost in GBP' => '10.00',
                'Stock' => '50',
                'Discontinued' => 'No'
            ]
        ];

        // Validate the data
        $validationResults = $this->stockValidator->validate($data);

        // Assertions
        $this->assertEquals(1, $validationResults['processed']);
        $this->assertEquals(1, $validationResults['successful']);
        $this->assertEmpty($validationResults['errors']);
        $this->assertCount(1, $validationResults['validatedData']);
    }

    /**
     * Test that invalid data is correctly skipped.
     * 
     * This test provides invalid data to the StockValidator and checks that it 
     * correctly skips the data, marking it as skipped and without any successful validation.
     * 
     * @test
     */
    public function it_skips_invalid_data()
    {
        // Sample invalid data
        $data = [
            [
                'Product Code' => 'P002',
                'Product Name' => 'Invalid Product',
                'Product Description' => 'Description',
                'Cost in GBP' => '2000.00', // Invalid cost
                'Stock' => '5',
                'Discontinued' => 'No'
            ]
        ];

        // Validate the data
        $validationResults = $this->stockValidator->validate($data);

        // Assertions
        $this->assertEquals(1, $validationResults['processed']);
        $this->assertEquals(0, $validationResults['successful']);
        $this->assertEquals(1, $validationResults['skipped']);
        $this->assertEmpty($validationResults['validatedData']);
    }
}