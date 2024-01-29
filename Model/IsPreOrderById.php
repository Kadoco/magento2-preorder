<?php
declare(strict_types=1);

namespace Kadoco\PreOrder\Model;

use Magento\Catalog\Model\ProductCategoryList;
use Kadoco\PreOrder\ViewModel\PreOrderConfigProvider;

class IsPreOrderById
{
    /**
     * @var ProductCategoryList
     */
    private ProductCategoryList $productCategoryList;
    /**
     * @var PreOrderConfigProvider
     */
    private PreOrderConfigProvider $preOrderConfigProvider;

    public function __construct(
        ProductCategoryList $productCategoryList,
        PreOrderConfigProvider $preOrderConfigProvider
    ) {
        $this->productCategoryList = $productCategoryList;
        $this->preOrderConfigProvider = $preOrderConfigProvider;
    }

    public function execute(int $productId):bool
    {
        if (!$this->preOrderConfigProvider->isActive()) {
            return false;
        }
        $productCategoryIds = $this->productCategoryList->getCategoryIds($productId);
        if (count($productCategoryIds) == 0) {
            return false;
        }
        $preOrderCategoryId = (int) $this->preOrderConfigProvider->getPreOrderCategoryId();
        $intersectionId = array_intersect($productCategoryIds, [$preOrderCategoryId]);
        if (count($intersectionId) === 0) {
            return false;
        }

        return true;
    }
}
