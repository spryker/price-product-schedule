<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\PriceProductSchedule\Business\Facade;

use Codeception\Test\Unit;
use DateTime;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\PriceProductScheduledApplyRequestTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\PriceTypeTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleFacadeInterface;
use SprykerTest\Zed\PriceProductSchedule\PriceProductScheduleBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group PriceProductSchedule
 * @group Business
 * @group Facade
 * @group PriceProductScheduleApplyAllScheduledPricesTest
 * Add your own group annotations below this line
 */
class PriceProductScheduleApplyAllScheduledPricesTest extends Unit
{
    protected PriceProductScheduleBusinessTester $tester;

    protected PriceProductScheduleFacadeInterface $priceProductScheduleFacade;

    protected StoreTransfer $storeTransfer;

    protected const int DEFAULT_BATCH_SIZE = 1;

    public function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureDatabaseTableIsEmpty();
        $this->storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
    }

    public function testApplyAllScheduledPricesWithoutProcessAllAppliesOneBatchPerStore(): void
    {
        // Arrange
        $productAbstractTransfer1 = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $this->tester->havePriceProductSchedule(
            $this->buildActivePriceProductScheduleData($productAbstractTransfer1->getIdProductAbstractOrFail()),
        );
        $this->tester->havePriceProductSchedule(
            $this->buildActivePriceProductScheduleData($productAbstractTransfer2->getIdProductAbstractOrFail()),
        );

        $applyRequestTransfer = (new PriceProductScheduledApplyRequestTransfer())
            ->setStoreName($this->storeTransfer->getNameOrFail())
            ->setBatchSize(static::DEFAULT_BATCH_SIZE);

        // Act
        $applyResponseTransfer = $this->tester->getFacade()->applyAllScheduledPrices($applyRequestTransfer);

        // Assert
        $this->assertCount(0, $applyResponseTransfer->getErrors());
        $this->assertSame(
            1,
            $this->tester->getPriceProductScheduleQuery()->filterByIsCurrent(true)->count(),
            'Only one batch (one price) should have been applied when processAll is not set.',
        );
    }

    public function testApplyAllScheduledPricesWithProcessAllAppliesAllBatchesPerStore(): void
    {
        // Arrange
        $productAbstractTransfer1 = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $this->tester->havePriceProductSchedule(
            $this->buildActivePriceProductScheduleData($productAbstractTransfer1->getIdProductAbstractOrFail()),
        );
        $this->tester->havePriceProductSchedule(
            $this->buildActivePriceProductScheduleData($productAbstractTransfer2->getIdProductAbstractOrFail()),
        );

        $applyRequestTransfer = (new PriceProductScheduledApplyRequestTransfer())
            ->setStoreName($this->storeTransfer->getNameOrFail())
            ->setBatchSize(static::DEFAULT_BATCH_SIZE)
            ->setProcessAll(true);

        // Act
        $applyResponseTransfer = $this->tester->getFacade()->applyAllScheduledPrices($applyRequestTransfer);

        // Assert
        $this->assertCount(0, $applyResponseTransfer->getErrors());
        $this->assertSame(
            2,
            $this->tester->getPriceProductScheduleQuery()->filterByIsCurrent(true)->count(),
            'All prices across all batches should have been applied when processAll is true.',
        );
    }

    public function testApplyAllScheduledPricesWithInvalidStoreNameReturnsError(): void
    {
        // Arrange
        $applyRequestTransfer = (new PriceProductScheduledApplyRequestTransfer())
            ->setStoreName('INVALID_STORE_XYZ');

        // Act
        $applyResponseTransfer = $this->tester->getFacade()->applyAllScheduledPrices($applyRequestTransfer);

        // Assert
        $this->assertGreaterThan(0, $applyResponseTransfer->getErrors()->count());
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<string, mixed>
     */
    protected function buildActivePriceProductScheduleData(int $idProductAbstract): array
    {
        $currencyId = $this->tester->haveCurrency();
        $priceType = $this->tester->havePriceType();

        return [
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $idProductAbstract,
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $priceType->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $priceType->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $this->storeTransfer->getIdStoreOrFail(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::GROSS_AMOUNT => 100,
                    MoneyValueTransfer::NET_AMOUNT => 80,
                ],
            ],
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-1 day')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 day')),
        ];
    }
}
