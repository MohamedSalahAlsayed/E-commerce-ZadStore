<?php

namespace App\Traits;

use App\Models\InventoryLog;

trait LogsInventory
{
    /**
     * Log a change in product inventory.
     *
     * @param int $productId
     * @param int $oldStock
     * @param int $newStock
     * @param string $type
     * @param string|null $notes
     * @param array|null $metadata
     * @return void
     */
    protected function logInventoryChange($productId, $oldStock, $newStock, $type, $notes = null, $metadata = null)
    {
        InventoryLog::create([
            'product_id' => $productId,
            'user_id' => auth()->id(),
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'change_amount' => $newStock - $oldStock,
            'type' => $type,
            'notes' => $notes,
            'metadata' => $metadata
        ]);
    }
}
