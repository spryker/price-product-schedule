<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\PriceProductSchedule\Business\Facade;

use Codeception\Test\Unit;
use DateTime;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\PriceTypeTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group PriceProductSchedule
 * @group Business
 * @group Facade
 * @group FindPriceProductScheduleByIdPriceProductScheduleListTest
 * Add your own group annotations below this line
 */
class FindPriceProductScheduleByIdPriceProductScheduleListTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\PriceProductSchedule\PriceProductScheduleBusinessTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleFacadeInterface
     */
    protected $priceProductScheduleFacade;

    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->tester->ensureDatabaseTableIsEmpty();
        $this->priceProductScheduleFacade = $this->tester->getFacade();
        $this->currencyFacade = $this->tester->getLocator()->currency()->facade();
        $this->storeFacade = $this->tester->getLocator()->store()->facade();
    }

    /**
     * @return void
     */
    public function testFindPriceProductScheduleByIdPriceProductScheduleListShouldReturnArrayWithScheduledPrices(): void
    {
        // Assign
        $productConcreteTransfer = $this->tester->haveProduct();
        $defaultPriceTypeTransfer = $this->tester->havePriceType();
        $storeTransfer = $this->storeFacade->getCurrentStore();
        $currencyId = $this->tester->haveCurrency([CurrencyTransfer::CODE => 'c22']);
        $currencyTransfer = $this->currencyFacade->getByIdCurrency($currencyId);
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();
        $this->tester->havePriceProduct([
            PriceProductTransfer::SKU_PRODUCT_ABSTRACT => $productConcreteTransfer->getAbstractSku(),
            PriceProductTransfer::PRICE_TYPE => $defaultPriceTypeTransfer,
            PriceProductTransfer::MONEY_VALUE => [
                MoneyValueTransfer::NET_AMOUNT => 100,
                MoneyValueTransfer::GROSS_AMOUNT => 100,
                MoneyValueTransfer::CURRENCY => $currencyTransfer,
            ],
        ]);
        $priceProductScheduleData = [
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $priceProductScheduleListTransfer,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $defaultPriceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $defaultPriceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 300,
                    MoneyValueTransfer::GROSS_AMOUNT => 300,
                ],
            ],
        ];
        $priceProductScheduleTransfer = $this->tester->havePriceProductSchedule($priceProductScheduleData);

        $priceProductScheduleData = [
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $priceProductScheduleListTransfer,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $defaultPriceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $defaultPriceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 400,
                    MoneyValueTransfer::GROSS_AMOUNT => 400,
                ],
            ],
        ];
        $this->tester->havePriceProductSchedule($priceProductScheduleData);

        // Act
        $foundPriceProductScheduleTransfers = $this->priceProductScheduleFacade
            ->findPriceProductSchedulesByIdPriceProductScheduleList($priceProductScheduleTransfer->getPriceProductScheduleList()->getIdPriceProductScheduleList());

        // Assert
        $this->assertCount(
            2,
            $foundPriceProductScheduleTransfers,
            'Count of scheduled prices does not match expected value',
        );
    }

    /**
     * @return void
     */
    public function testFindPriceProductScheduleByIdPriceProductScheduleListShouldReturnEmptyArray(): void
    {
        // Assign
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();

        // Act
        $foundPriceProductScheduleTransfers = $this->priceProductScheduleFacade
            ->findPriceProductSchedulesByIdPriceProductScheduleList($priceProductScheduleListTransfer->getIdPriceProductScheduleList());

        // Assert
        $this->assertCount(
            0,
            $foundPriceProductScheduleTransfers,
            'Count of scheduled prices does not match expected value',
        );
    }
}
