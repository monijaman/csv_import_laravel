<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

/**
 * Class StockRepository
 * 
 * This repository handles interactions with the 'stock' table in the database.
 */
class StockRepository
{
    /**
     * Insert a new record into the 'stock' table.
     * 
     * @param array $data - The data to be inserted.
     * @return bool - Returns true if the insert was successful.
     */
    public function insert(array $data)
    {
        return DB::table('stock')->insert($data);
    }

    public function exists($productCode)
    {
        return DB::table('stock')->where('product_code', $productCode)->exists();
    }
}
