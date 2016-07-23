<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Magento\Catalog\Model;

class Product
{
    /** @var \Magento\Catalog\Model\StockItem */
    protected $_stockItem;

    /**
     * @param StockItem $stockItem
     */
    public function __construct(StockItem $stockItem)
    {
        $this->_stockItem = $stockItem;
    }

    /**
     * @return StockItem
     */
    public function getStockItem()
    {
        return $this->_stockItem;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->getStockItem()->getWarehouse();
    }

    /**
     * @param \Magento\Catalog\Model\StockItem $stockItem
     * @param int $id
     * @return int
     */
    public function getStockItemWarehouseId(StockItem $stockItem, $id)
    {
        $warehouse = $stockItem->getWarehouse();
        return $warehouse->getId();
    }
}