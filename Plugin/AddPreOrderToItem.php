<?php
declare(strict_types=1);

namespace Kadoco\PreOrder\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\CustomerData\AbstractItem;
use Kadoco\PreOrder\Model\IsProductPreOrder;

class AddPreOrderToItem
{
    private IsProductPreOrder $isProductPreOrder;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        IsProductPreOrder $isProductPreOrder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->isProductPreOrder = $isProductPreOrder;
        $this->productRepository = $productRepository;
    }

    public function afterGetItemData(
        AbstractItem $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $product = $item->getProduct();
        if (!$this->isProductPreOrder->execute($product)) {
            $preOrderDate = "";
        } else {
            $preOrderDate = $this->isProductPreOrder->getPreOrderDate($product);
            if ($preOrderDate) {
                $preOrderDate = __('Estimated:') . ' ' . $preOrderDate;
            }
        }
        $result['pre_order'] = $preOrderDate;

        return $result;
    }
}
