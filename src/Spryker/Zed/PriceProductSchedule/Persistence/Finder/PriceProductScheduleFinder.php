<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Finder;

use DateTime;
use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleExportItemTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Orm\Zed\Currency\Persistence\Map\SpyCurrencyTableMap;
use Orm\Zed\PriceProduct\Persistence\Map\SpyPriceTypeTableMap;
use Orm\Zed\PriceProductSchedule\Persistence\Map\SpyPriceProductScheduleTableMap;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductSchedule;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Store\Persistence\Map\SpyStoreTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface;

class PriceProductScheduleFinder implements PriceProductScheduleFinderInterface
{
    /**
     * @var \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery
     */
    protected $priceProductScheduleQuery;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface
     */
    protected $priceProductScheduleMapper;

    public function __construct(SpyPriceProductScheduleQuery $priceProductScheduleQuery, PriceProductScheduleMapperInterface $priceProductScheduleMapper)
    {
        $this->priceProductScheduleQuery = $priceProductScheduleQuery;
        $this->priceProductScheduleMapper = $priceProductScheduleMapper;
    }

    /**
     * @module Currency
     * @module PriceProduct
     * @module Store
     * @module Product
     *
     * @param \Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
     *
     * @return int
     */
    public function findCountPriceProductScheduleByCriteriaFilter(
        PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
    ): int {
        /** @var \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $query */
        $query = $this->priceProductScheduleQuery
            ->joinWithCurrency()
            ->useCurrencyQuery()
                ->filterByCode($priceProductScheduleCriteriaFilterTransfer->getCurrencyCode())
            ->endUse();

        /** @var \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $query */
        $query = $query
            ->joinWithPriceType()
            ->usePriceTypeQuery()
                ->filterByName($priceProductScheduleCriteriaFilterTransfer->getPriceTypeName())
            ->endUse();

        /** @var \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $query */
        $query = $query
            ->filterByNetPrice($priceProductScheduleCriteriaFilterTransfer->getNetAmount())
            ->filterByGrossPrice($priceProductScheduleCriteriaFilterTransfer->getGrossAmount())
            ->filterByActiveFrom(new DateTime($priceProductScheduleCriteriaFilterTransfer->getActiveFrom()))
            ->filterByActiveTo(new DateTime($priceProductScheduleCriteriaFilterTransfer->getActiveTo()))
            ->joinWithPriceProductScheduleList()
            ->joinWithStore()
            ->useStoreQuery()
                ->filterByName($priceProductScheduleCriteriaFilterTransfer->getStoreName())
            ->endUse();

        if ($priceProductScheduleCriteriaFilterTransfer->getSkuProductAbstract() !== null) {
            $query
                ->joinWithProductAbstract()
                ->useProductAbstractQuery()
                ->filterBySku($priceProductScheduleCriteriaFilterTransfer->getSkuProductAbstract())
                ->endUse();
        }

        if ($priceProductScheduleCriteriaFilterTransfer->getSkuProduct() !== null) {
            $query
                ->joinWithProduct()
                ->useProductQuery()
                ->filterBySku($priceProductScheduleCriteriaFilterTransfer->getSkuProduct())
                ->endUse();
        }

        return $query->count();
    }

    public function findPriceProductScheduleById(int $idPriceProductSchedule): ?PriceProductScheduleTransfer
    {
        $priceProductScheduleEntity = $this->priceProductScheduleQuery
            ->filterByIdPriceProductSchedule($idPriceProductSchedule)
            ->findOne();

        if ($priceProductScheduleEntity === null) {
            return null;
        }

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntityToPriceProductScheduleTransfer($priceProductScheduleEntity, new PriceProductScheduleTransfer());
    }

    public function isPriceProductScheduleUnique(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): bool {
        $priceProductScheduleTransfer->requirePriceProduct()
            ->getPriceProduct()
            ->requireMoneyValue();

        $priceProductScheduleEntity = $this->priceProductScheduleMapper
            ->mapPriceProductScheduleTransferToPriceProductScheduleEntity($priceProductScheduleTransfer, new SpyPriceProductSchedule());

        $priceProductScheduleQuery = $this->priceProductScheduleQuery
            ->filterByActiveFrom($priceProductScheduleEntity->getActiveFrom())
            ->filterByActiveTo($priceProductScheduleEntity->getActiveTo())
            ->filterByNetPrice($priceProductScheduleEntity->getNetPrice())
            ->filterByGrossPrice($priceProductScheduleEntity->getGrossPrice())
            ->filterByFkCurrency($priceProductScheduleEntity->getFkCurrency())
            ->filterByFkStore($priceProductScheduleEntity->getFkStore())
            ->filterByFkPriceType($priceProductScheduleEntity->getFkPriceType())
            ->filterByIdPriceProductSchedule($priceProductScheduleEntity->getIdPriceProductSchedule(), Criteria::NOT_EQUAL);

        $priceProductScheduleQuery = $this->addProductIdentifierToUniqueQuery(
            $priceProductScheduleEntity,
            $priceProductScheduleQuery,
        );

        return $priceProductScheduleQuery->count() === 0;
    }

    /**
     * @module Store
     * @module Currency
     * @module PriceProduct
     * @module Product
     *
     * @param int $idPriceProductScheduleList
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesByIdPriceProductScheduleList(
        int $idPriceProductScheduleList
    ): array {
        $priceProductScheduleEntityCollection = $this->priceProductScheduleQuery
            ->leftJoinWithStore()
            ->leftJoinWithCurrency()
            ->leftJoinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->filterByFkPriceProductScheduleList($idPriceProductScheduleList)
            ->find()
            ->getData();

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers(
                $priceProductScheduleEntityCollection,
            );
    }

    /**
     * @module Store
     * @module Currency
     * @module PriceProduct
     * @module Product
     *
     * @param int $idPriceProductScheduleList
     * @param int $lastProcessedId
     * @param int $limit
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleExportItemTransfer>
     */
    public function findPriceProductScheduleExportItemsByIdPriceProductScheduleList(
        int $idPriceProductScheduleList,
        int $lastProcessedId,
        int $limit
    ): array {
        /** @var array<array<string, string|null>> $rows */
        $rows = $this->priceProductScheduleQuery
            ->filterByFkPriceProductScheduleList($idPriceProductScheduleList)
            ->filterByIdPriceProductSchedule($lastProcessedId, Criteria::GREATER_THAN)
            ->joinWithCurrency()
            ->joinWithStore()
            ->joinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->withColumn(SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE, PriceProductScheduleExportItemTransfer::ID_PRICE_PRODUCT_SCHEDULE)
            ->withColumn(SpyProductAbstractTableMap::COL_SKU, PriceProductScheduleExportItemTransfer::ABSTRACT_SKU)
            ->withColumn(SpyProductTableMap::COL_SKU, PriceProductScheduleExportItemTransfer::CONCRETE_SKU)
            ->withColumn(SpyCurrencyTableMap::COL_CODE, PriceProductScheduleExportItemTransfer::CURRENCY_CODE)
            ->withColumn(SpyStoreTableMap::COL_NAME, PriceProductScheduleExportItemTransfer::STORE_NAME)
            ->withColumn(SpyPriceTypeTableMap::COL_NAME, PriceProductScheduleExportItemTransfer::PRICE_TYPE_NAME)
            ->withColumn(SpyPriceProductScheduleTableMap::COL_NET_PRICE, PriceProductScheduleExportItemTransfer::NET_AMOUNT)
            ->withColumn(SpyPriceProductScheduleTableMap::COL_GROSS_PRICE, PriceProductScheduleExportItemTransfer::GROSS_AMOUNT)
            ->withColumn(SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM, PriceProductScheduleExportItemTransfer::ACTIVE_FROM)
            ->withColumn(SpyPriceProductScheduleTableMap::COL_ACTIVE_TO, PriceProductScheduleExportItemTransfer::ACTIVE_TO)
            ->select([
                PriceProductScheduleExportItemTransfer::ID_PRICE_PRODUCT_SCHEDULE,
                PriceProductScheduleExportItemTransfer::ABSTRACT_SKU,
                PriceProductScheduleExportItemTransfer::CONCRETE_SKU,
                PriceProductScheduleExportItemTransfer::CURRENCY_CODE,
                PriceProductScheduleExportItemTransfer::STORE_NAME,
                PriceProductScheduleExportItemTransfer::PRICE_TYPE_NAME,
                PriceProductScheduleExportItemTransfer::NET_AMOUNT,
                PriceProductScheduleExportItemTransfer::GROSS_AMOUNT,
                PriceProductScheduleExportItemTransfer::ACTIVE_FROM,
                PriceProductScheduleExportItemTransfer::ACTIVE_TO,
            ])
            ->orderByIdPriceProductSchedule()
            ->setLimit($limit)
            ->find()
            ->getData();

        return $this->mapRowsToPriceProductScheduleExportItemTransfers($rows);
    }

    /**
     * @param array<array<string, string|null>> $rows
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleExportItemTransfer>
     */
    protected function mapRowsToPriceProductScheduleExportItemTransfers(array $rows): array
    {
        $transfers = [];

        foreach ($rows as $row) {
            $transfers[] = (new PriceProductScheduleExportItemTransfer())->fromArray($row, true);
        }

        return $transfers;
    }

    protected function addProductIdentifierToUniqueQuery(
        SpyPriceProductSchedule $priceProductScheduleEntity,
        SpyPriceProductScheduleQuery $priceProductScheduleQuery
    ): SpyPriceProductScheduleQuery {
        $idProduct = $priceProductScheduleEntity->getFkProduct();
        if ($idProduct !== null) {
            return $priceProductScheduleQuery->filterByFkProduct($idProduct);
        }

        $idProductAbstract = $priceProductScheduleEntity->getFkProductAbstract();
        if ($idProductAbstract !== null) {
            return $priceProductScheduleQuery->filterByFkProductAbstract($idProductAbstract);
        }

        return $priceProductScheduleQuery;
    }
}
