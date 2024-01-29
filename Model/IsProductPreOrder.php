<?php
declare(strict_types=1);

namespace Kadoco\PreOrder\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Kadoco\PreOrder\ViewModel\PreOrderConfigProvider;

class IsProductPreOrder implements ArgumentInterface
{
    /**
     * @var PreOrderConfigProvider
     */
    private PreOrderConfigProvider $preOrderConfigProvider;
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        PreOrderConfigProvider $preOrderConfigProvider,
        ProductRepositoryInterface $productRepository
    ) {
        $this->preOrderConfigProvider = $preOrderConfigProvider;
        $this->productRepository = $productRepository;
    }

    public function getProductBySku($sku)
    {
        return $this->productRepository->get($sku);
    }

    public function getProductByItem($item)
    {
        $sku = $item->getSku();

        return $this->getProductBySku($sku);
    }

    public function execute(ProductInterface $product):bool
    {
        $productCategoryIds = $product->getCategoryIds();
        if (count($productCategoryIds) == 0) {
            return false;
        }
        if (!$this->preOrderConfigProvider->isActive()) {
            return false;
        }

        $preOrderCategoryId = (int) $this->preOrderConfigProvider->getPreOrderCategoryId();
        $intersectionId = array_intersect($productCategoryIds, [$preOrderCategoryId]);
        if (count($intersectionId) === 0) {
            return false;
        }

        return true;
    }

    public function getPreOrderDate(ProductInterface $product):?string
    {
        try {
            $product = $this->productRepository->getById((int)$product->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
        $preOrderDate = $product->getData('pre_order_date');
        if (!$preOrderDate) {
            return null;
        }
        $preOrderDate = substr($preOrderDate,0,10);
        if (!$preOrderDate) {
            return null;
        }

        return date('d. F Y', strtotime($preOrderDate));
    }

    public function getPreOrderClass(ProductInterface $product):string
    {
        if (!$this->execute($product)) {
            return "";
        }

        return "preorder";
    }
}
