<?php

namespace MobileApp\Connector\Block;

/**
 * Connector content block
 */
class Connector extends \Magento\Framework\View\Element\Template
{
    /**
     * Connector collection
     *
     * @var MobileApp\Connector\Model\ResourceModel\Connector\Collection
     */
    protected $_connectorCollection = null;
    
    /**
     * Connector factory
     *
     * @var \MobileApp\Connector\Model\ConnectorFactory
     */
    protected $_connectorCollectionFactory;
    
    /** @var \MobileApp\Connector\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MobileApp\Connector\Model\ResourceModel\Connector\CollectionFactory $connectorCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MobileApp\Connector\Model\ResourceModel\Connector\CollectionFactory $connectorCollectionFactory,
        \MobileApp\Connector\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_connectorCollectionFactory = $connectorCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve connector collection
     *
     * @return MobileApp\Connector\Model\ResourceModel\Connector\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_connectorCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared connector collection
     *
     * @return MobileApp\Connector\Model\ResourceModel\Connector\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_connectorCollection)) {
            $this->_connectorCollection = $this->_getCollection();
            $this->_connectorCollection->setCurPage($this->getCurrentPage());
            $this->_connectorCollection->setPageSize($this->_dataHelper->getConnectorPerPage());
            $this->_connectorCollection->setOrder('published_at','asc');
        }

        return $this->_connectorCollection;
    }
    
    /**
     * Fetch the current page for the connector list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param MobileApp\Connector\Model\Connector $connectorItem
     * @return string
     */
    public function getItemUrl($connectorItem)
    {
        return $this->getUrl('*/*/view', array('id' => $connectorItem->getId()));
    }
    
    /**
     * Return URL for resized Connector Item image
     *
     * @param MobileApp\Connector\Model\Connector $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('connector_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $connectorPerPage = $this->_dataHelper->getConnectorPerPage();

            $pager->setAvailableLimit([$connectorPerPage => $connectorPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
}
