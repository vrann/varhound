<?php
/**
 * @author Extensibility DL-X-Extensibility-Team@corp.ebay.com
 */

namespace Magento\Catalog\Model;

class StockItem
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        $warehouse = $this->_initWarehouse();
        return $warehouse;
    }


    /**
     * @return \Magento\Catalog\Model\Warehouse
     */
    protected function _initWarehouse()
    {
        $this->_objectManager->get('Magento\Catalog\Model\Warehouse');
    }

}