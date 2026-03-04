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
use Generated\Shared\Transfer\PriceProductScheduleExportItemCriteriaTransfer;
use Generated\Shared\Transfer\PriceProductScheduleExportItemTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\PriceTypeTransfer;
use Generated\Shared\Transfer\StoreTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group PriceProductSchedule
 * @group Business
 * @group Facade
 * @group GetPriceProductScheduleExportItemCollectionTest
 * Add your own group annotations below this line
 */
class GetPriceProductScheduleExportItemCollectionTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\PriceProductSchedule\PriceProductScheduleBusinessTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleFacadeInterface
     */
    protected $priceProductScheduleFacade;

    public function setUp(): void
    {
        parent::setUp();
        $this->tester->ensureDatabaseTableIsEmpty();
        $this->priceProductScheduleFacade = $this->tester->getFacade();
    }

    public function testReturnsExportItemsForGivenScheduleList(): void
    {
        // Arrange
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();
        $productConcreteTransfer = $this->tester->haveProduct();
        $priceTypeTransfer = $this->tester->havePriceType();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $currencyId = $this->tester->haveCurrency([CurrencyTransfer::CODE => 'e01']);
        $currencyFacade = $this->tester->getLocator()->currency()->facade();
        $currencyTransfer = $currencyFacade->getByIdCurrency($currencyId);

        $this->tester->havePriceProductSchedule([
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $priceProductScheduleListTransfer,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $priceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $priceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 300,
                    MoneyValueTransfer::GROSS_AMOUNT => 400,
                ],
            ],
        ]);

        $criteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($priceProductScheduleListTransfer->getIdPriceProductScheduleList())
            ->setLastProcessedId(0)
            ->setLimit(100);

        // Act
        $collectionTransfer = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($criteriaTransfer);

        // Assert
        $exportItems = $collectionTransfer->getPriceProductScheduleExportItems();
        $this->assertCount(1, $exportItems);
        $this->assertInstanceOf(PriceProductScheduleExportItemTransfer::class, $exportItems[0]);
        $this->assertSame($storeTransfer->getName(), $exportItems[0]->getStoreName());
        $this->assertSame($currencyTransfer->getCode(), $exportItems[0]->getCurrencyCode());
        $this->assertSame($priceTypeTransfer->getName(), $exportItems[0]->getPriceTypeName());
        $this->assertSame(300, $exportItems[0]->getNetAmount());
        $this->assertSame(400, $exportItems[0]->getGrossAmount());
        $this->assertNotNull($exportItems[0]->getAbstractSku(), 'Abstract-only schedule should have abstractSku');
        $this->assertNull($exportItems[0]->getConcreteSku(), 'Abstract-only schedule should have null concreteSku');
    }

    public function testReturnsConcreteSkuWhenScheduledForConcreteProduct(): void
    {
        // Arrange
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();
        $productConcreteTransfer = $this->tester->haveProduct();
        $priceTypeTransfer = $this->tester->havePriceType();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $currencyId = $this->tester->haveCurrency([CurrencyTransfer::CODE => 'e04']);
        $currencyFacade = $this->tester->getLocator()->currency()->facade();
        $currencyTransfer = $currencyFacade->getByIdCurrency($currencyId);

        $this->tester->havePriceProductSchedule([
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $priceProductScheduleListTransfer,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT => $productConcreteTransfer->getIdProductConcrete(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $priceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $priceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 500,
                    MoneyValueTransfer::GROSS_AMOUNT => 600,
                ],
            ],
        ]);

        $criteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($priceProductScheduleListTransfer->getIdPriceProductScheduleList())
            ->setLastProcessedId(0)
            ->setLimit(100);

        // Act
        $collectionTransfer = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($criteriaTransfer);

        // Assert
        $exportItems = $collectionTransfer->getPriceProductScheduleExportItems();
        $this->assertCount(1, $exportItems);
        $this->assertSame($productConcreteTransfer->getSku(), $exportItems[0]->getConcreteSku());
        $this->assertNull($exportItems[0]->getAbstractSku(), 'Concrete schedule should have null abstractSku');
        $this->assertSame(500, $exportItems[0]->getNetAmount());
        $this->assertSame(600, $exportItems[0]->getGrossAmount());
    }

    public function testReturnsEmptyCollectionForEmptyScheduleList(): void
    {
        // Arrange
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();

        $criteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($priceProductScheduleListTransfer->getIdPriceProductScheduleList())
            ->setLastProcessedId(0)
            ->setLimit(100);

        // Act
        $collectionTransfer = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($criteriaTransfer);

        // Assert
        $this->assertCount(0, $collectionTransfer->getPriceProductScheduleExportItems());
    }

    public function testPaginatesResultsUsingLastProcessedId(): void
    {
        // Arrange
        $priceProductScheduleListTransfer = $this->tester->havePriceProductScheduleList();
        $productConcreteTransfer = $this->tester->haveProduct();
        $priceTypeTransfer = $this->tester->havePriceType();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $currencyId = $this->tester->haveCurrency([CurrencyTransfer::CODE => 'e02']);
        $currencyFacade = $this->tester->getLocator()->currency()->facade();
        $currencyTransfer = $currencyFacade->getByIdCurrency($currencyId);

        $baseData = [
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $priceProductScheduleListTransfer,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $priceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $priceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 100,
                    MoneyValueTransfer::GROSS_AMOUNT => 100,
                ],
            ],
        ];

        $this->tester->havePriceProductSchedule($baseData);
        $this->tester->havePriceProductSchedule($baseData);
        $this->tester->havePriceProductSchedule($baseData);

        $idPriceProductScheduleList = $priceProductScheduleListTransfer->getIdPriceProductScheduleList();

        // Act - first page (limit 2)
        $firstPageCriteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($idPriceProductScheduleList)
            ->setLastProcessedId(0)
            ->setLimit(2);

        $firstPageCollection = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($firstPageCriteriaTransfer);

        $firstPageItems = $firstPageCollection->getPriceProductScheduleExportItems();
        $lastIdFromFirstPage = $firstPageItems[$firstPageItems->count() - 1]->getIdPriceProductSchedule();

        // Act - second page (from cursor)
        $secondPageCriteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($idPriceProductScheduleList)
            ->setLastProcessedId($lastIdFromFirstPage)
            ->setLimit(2);

        $secondPageCollection = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($secondPageCriteriaTransfer);

        $secondPageItems = $secondPageCollection->getPriceProductScheduleExportItems();

        // Assert
        $this->assertCount(2, $firstPageItems);
        $this->assertCount(1, $secondPageItems);
        $this->assertGreaterThan($lastIdFromFirstPage, $secondPageItems[0]->getIdPriceProductSchedule());
    }

    public function testDoesNotReturnItemsFromOtherScheduleLists(): void
    {
        // Arrange
        $scheduleList1 = $this->tester->havePriceProductScheduleList();
        $scheduleList2 = $this->tester->havePriceProductScheduleList();
        $productConcreteTransfer = $this->tester->haveProduct();
        $priceTypeTransfer = $this->tester->havePriceType();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $currencyId = $this->tester->haveCurrency([CurrencyTransfer::CODE => 'e03']);
        $currencyFacade = $this->tester->getLocator()->currency()->facade();
        $currencyTransfer = $currencyFacade->getByIdCurrency($currencyId);

        $baseData = [
            PriceProductScheduleTransfer::ACTIVE_FROM => (new DateTime('-4 days')),
            PriceProductScheduleTransfer::ACTIVE_TO => (new DateTime('+1 hour')),
            PriceProductScheduleTransfer::IS_CURRENT => false,
            PriceProductScheduleTransfer::PRICE_PRODUCT => [
                PriceProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
                PriceProductTransfer::PRICE_TYPE => [
                    PriceTypeTransfer::NAME => $priceTypeTransfer->getName(),
                    PriceTypeTransfer::ID_PRICE_TYPE => $priceTypeTransfer->getIdPriceType(),
                ],
                PriceProductTransfer::MONEY_VALUE => [
                    MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStore(),
                    MoneyValueTransfer::FK_CURRENCY => $currencyId,
                    MoneyValueTransfer::CURRENCY => $currencyTransfer,
                    MoneyValueTransfer::NET_AMOUNT => 100,
                    MoneyValueTransfer::GROSS_AMOUNT => 100,
                ],
            ],
        ];

        $this->tester->havePriceProductSchedule(
            $baseData + [PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $scheduleList1],
        );
        $this->tester->havePriceProductSchedule(
            $baseData + [PriceProductScheduleTransfer::PRICE_PRODUCT_SCHEDULE_LIST => $scheduleList2],
        );

        $criteriaTransfer = (new PriceProductScheduleExportItemCriteriaTransfer())
            ->setIdPriceProductScheduleList($scheduleList1->getIdPriceProductScheduleList())
            ->setLastProcessedId(0)
            ->setLimit(100);

        // Act
        $collectionTransfer = $this->priceProductScheduleFacade
            ->getPriceProductScheduleExportItemCollection($criteriaTransfer);

        // Assert
        $this->assertCount(1, $collectionTransfer->getPriceProductScheduleExportItems());
    }
}
