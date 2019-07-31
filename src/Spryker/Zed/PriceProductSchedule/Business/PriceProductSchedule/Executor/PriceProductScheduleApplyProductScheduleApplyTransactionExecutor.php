<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Executor;

use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleDisablerInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleWriterInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface;

class PriceProductScheduleApplyProductScheduleApplyTransactionExecutor implements PriceProductScheduleApplyTransactionExecutorInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleDisablerInterface
     */
    protected $priceProductScheduleDisabler;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface
     */
    protected $priceProductFacade;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleWriterInterface
     */
    protected $priceProductScheduleWriter;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleDisablerInterface $priceProductScheduleDisabler
     * @param \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface $priceProductFacade
     * @param \Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleWriterInterface $priceProductScheduleWriter
     */
    public function __construct(PriceProductScheduleDisablerInterface $priceProductScheduleDisabler, PriceProductScheduleToPriceProductFacadeInterface $priceProductFacade, PriceProductScheduleWriterInterface $priceProductScheduleWriter)
    {
        $this->priceProductScheduleDisabler = $priceProductScheduleDisabler;
        $this->priceProductFacade = $priceProductFacade;
        $this->priceProductScheduleWriter = $priceProductScheduleWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer[] $priceProductScheduleForEnable
     *
     * @return void
     */
    public function executeApplyScheduledPriceTransaction(array $priceProductScheduleForEnable): void
    {
        foreach ($priceProductScheduleForEnable as $priceProductScheduleTransfer) {
            $this->getTransactionHandler()->handleTransaction(function () use ($priceProductScheduleTransfer): void {
                $this->executeApplyScheduledPrices($priceProductScheduleTransfer);
            });
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer $priceProductScheduleTransfer
     *
     * @return void
     */
    protected function executeApplyScheduledPrices(PriceProductScheduleTransfer $priceProductScheduleTransfer): void
    {
        $this->priceProductScheduleDisabler->disableNotRelevantPriceProductSchedulesByPriceProductSchedule($priceProductScheduleTransfer);

        $priceProductTransfer = $this->preparePriceProductTransferForPersist($priceProductScheduleTransfer);
        $this->priceProductFacade->persistPriceProductStore($priceProductTransfer);

        $priceProductScheduleTransfer->setIsCurrent(true);

        $this->priceProductScheduleWriter->savePriceProductSchedule($priceProductScheduleTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer $priceProductScheduleTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer
     */
    protected function preparePriceProductTransferForPersist(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): PriceProductTransfer {
        $priceProductScheduleTransfer->requirePriceProduct();
        $priceProductTransfer = $priceProductScheduleTransfer->getPriceProduct();

        $priceProductTransfer->requireMoneyValue();
        $moneyValueTransfer = $priceProductTransfer->getMoneyValue();

        $priceProductTransferForPersist = $this->getPriceProductForPersist($priceProductTransfer);

        $priceProductTransferForPersist->getMoneyValue()
            ->setGrossAmount($moneyValueTransfer->getGrossAmount())
            ->setNetAmount($moneyValueTransfer->getNetAmount());

        return $priceProductTransferForPersist;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer
     */
    protected function getPriceProductForPersist(PriceProductTransfer $priceProductTransfer): PriceProductTransfer
    {
        $priceProductTransfer
            ->requireMoneyValue()
            ->requirePriceType();

        $moneyValueTransfer = $priceProductTransfer->getMoneyValue();
        $moneyValueTransfer->requireCurrency();

        $priceProductFilterTransfer = (new PriceProductFilterTransfer())
            ->setPriceTypeName($priceProductTransfer->getPriceType()->getName())
            ->setCurrencyIsoCode($moneyValueTransfer->getCurrency()->getCode());

        if ($priceProductTransfer->getSkuProductAbstract() !== null) {
            $priceProductFilterTransfer->setSku($priceProductTransfer->getSkuProductAbstract());
        }

        if ($priceProductTransfer->getSkuProduct() !== null) {
            $priceProductFilterTransfer->setSku($priceProductTransfer->getSkuProduct());
        }

        $priceProductTransfersForUpdate = $this->priceProductFacade->findPriceProductFor(
            $priceProductFilterTransfer
        );

        return $priceProductTransfersForUpdate ?? $priceProductTransfer;
    }
}
