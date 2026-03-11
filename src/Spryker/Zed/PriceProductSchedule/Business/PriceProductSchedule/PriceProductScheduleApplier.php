<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Exception;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\PriceProductScheduledApplyRequestTransfer;
use Generated\Shared\Transfer\PriceProductScheduledApplyResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\InstancePoolingTrait;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Executor\PriceProductScheduleApplyTransactionExecutorInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToStoreFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface;

class PriceProductScheduleApplier implements PriceProductScheduleApplierInterface
{
    use TransactionTrait;
    use InstancePoolingTrait;

    public function __construct(
        protected PriceProductScheduleDisablerInterface $priceProductScheduleDisabler,
        protected PriceProductScheduleRepositoryInterface $priceProductScheduleRepository,
        protected PriceProductScheduleToStoreFacadeInterface $storeFacade,
        protected PriceProductScheduleApplyTransactionExecutorInterface $applyScheduledPriceTransactionExecutor
    ) {
    }

    public function applyScheduledPrices(?string $storeName = null): void
    {
        $productSchedulePricesForEnable = $this->resolvePriceProductSchedulesToEnable($storeName);

        $this->applyScheduledPriceTransactionExecutor->execute($productSchedulePricesForEnable);

        $this->priceProductScheduleDisabler->disableNotActiveScheduledPrices();
    }

    /**
     * @param string|null $storeName
     *
     * @throws \Exception
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    protected function resolvePriceProductSchedulesToEnable(?string $storeName = null): array
    {
        if ($storeName) {
            $storeTransfer = $this->storeFacade->findStoreByName($storeName);

            if (!$storeTransfer) {
                throw new Exception("Store $storeName not found.");
            }

            return $this->priceProductScheduleRepository->findPriceProductSchedulesToEnableByStore($storeTransfer);
        }

        if ($this->storeFacade->isCurrentStoreDefined()) {
            return $this->priceProductScheduleRepository->findPriceProductSchedulesToEnableByStore($this->storeFacade->getCurrentStore());
        }

        $productSchedulePricesForEnable = [];
        foreach ($this->storeFacade->getAllStores() as $storeTransfer) {
            $productSchedulePricesForEnable[] = $this->priceProductScheduleRepository->findPriceProductSchedulesToEnableByStore($storeTransfer);
        }

        return array_merge(...$productSchedulePricesForEnable);
    }

    public function applyAllScheduledPrices(
        PriceProductScheduledApplyRequestTransfer $priceProductScheduledApplyRequestTransfer
    ): PriceProductScheduledApplyResponseTransfer {
        $priceProductScheduledApplyResponseTransfer = (new PriceProductScheduledApplyResponseTransfer());

        $this->disableInstancePooling();

        $storeTransfers = $this->resolveStoresToProcess($priceProductScheduledApplyRequestTransfer, $priceProductScheduledApplyResponseTransfer);
        foreach ($storeTransfers as $storeTransfer) {
            $generator = $this->priceProductScheduleRepository->getPriceProductSchedulesToEnableByStoreGenerator($storeTransfer, $priceProductScheduledApplyRequestTransfer->getBatchSizeOrFail());

            foreach ($generator as $priceProductScheduleBatch) {
                $this->applyScheduledPriceTransactionExecutor->execute($priceProductScheduleBatch);
                unset($priceProductScheduleBatch);

                if (!$priceProductScheduledApplyRequestTransfer->getProcessAll()) {
                    break;
                }
            }
        }

        $this->priceProductScheduleDisabler->disableNotActiveScheduledPrices();

        return $priceProductScheduledApplyResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduledApplyRequestTransfer $priceProductScheduledApplyRequestTransfer
     * @param \Generated\Shared\Transfer\PriceProductScheduledApplyResponseTransfer $priceProductScheduledApplyResponseTransfer
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    protected function resolveStoresToProcess(
        PriceProductScheduledApplyRequestTransfer $priceProductScheduledApplyRequestTransfer,
        PriceProductScheduledApplyResponseTransfer $priceProductScheduledApplyResponseTransfer
    ): array {
        $storeName = $priceProductScheduledApplyRequestTransfer->getStoreName();

        if ($storeName) {
            return $this->resolveStoreByName($storeName, $priceProductScheduledApplyResponseTransfer);
        }

        if ($this->storeFacade->isCurrentStoreDefined()) {
            return [$this->storeFacade->getCurrentStore()];
        }

        return $this->storeFacade->getAllStores();
    }

    /**
     * @param string $storeName
     * @param \Generated\Shared\Transfer\PriceProductScheduledApplyResponseTransfer $priceProductScheduledApplyResponseTransfer
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    protected function resolveStoreByName(
        string $storeName,
        PriceProductScheduledApplyResponseTransfer $priceProductScheduledApplyResponseTransfer
    ): array {
        $storeTransfer = $this->storeFacade->findStoreByName($storeName);

        if (!$storeTransfer) {
            $priceProductScheduledApplyResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(sprintf('Store "%s" not found.', $storeName)),
            );

            return [];
        }

        return [$storeTransfer];
    }
}
