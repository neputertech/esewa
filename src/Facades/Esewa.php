<?php
namespace Neputer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed initiate(string $return_url, string $purchase_order_id, int $amount, ?array $customer_info, ?array amount_breakdown, ?array $product_details)
 */
class Esewa extends Facade {
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'esewa';
    }
}
