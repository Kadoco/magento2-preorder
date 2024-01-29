<?php
declare(strict_types=1);

namespace Kadoco\PreOrder\Model;

use Magento\Catalog\Model\CategoryLinkRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Kadoco\PreOrder\ViewModel\PreOrderConfigProvider;

class RemoveProductFromPreOrder
{
    /**
     * @var CategoryLinkRepository
     */
    private CategoryLinkRepository $categoryLinkRepository;
    private PreOrderConfigProvider $preOrderConfigProvider;

    public function __construct(
        CategoryLinkRepository $categoryLinkRepository,
        PreOrderConfigProvider $preOrderConfigProvider
    ) {
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->preOrderConfigProvider = $preOrderConfigProvider;
    }

    public function execute(string $sku):void
    {
        if (!$this->preOrderConfigProvider->isActive()) {
            return;
        }
        $categoryId = (int) $this->preOrderConfigProvider->getPreOrderCategoryId();

        try {
            $this->categoryLinkRepository->deleteByIds($categoryId, $sku);
        } catch (CouldNotSaveException $e) {
        } catch (InputException $e) {
        } catch (StateException $e) {
        }
    }
}
