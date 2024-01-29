<?php
declare(strict_types=1);

namespace Kadoco\PreOrder\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class PreOrderConfigProvider implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function isActive():bool
    {
        return (bool) $this->getConfigValue('preorder/configuration/active');
    }

    public function getPreOrderCategoryId():int
    {
        return (int) $this->getConfigValue('preorder/configuration/rootid');
    }


    private function getConfigValue(string $path): string
    {
        $storeId = $this->storeManager->getStore()->getId();

        return (string) $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
