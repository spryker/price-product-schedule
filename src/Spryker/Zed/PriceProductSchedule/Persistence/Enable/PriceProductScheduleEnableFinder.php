<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Enable;

use Generated\Shared\Transfer\StoreTransfer;
use Generator;
use Orm\Zed\PriceProductSchedule\Persistence\Map\SpyPriceProductScheduleListTableMap;
use Orm\Zed\PriceProductSchedule\Persistence\Map\SpyPriceProductScheduleTableMap;
use PDO;
use Propel\Runtime\Connection\ConnectionInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPropelFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductSchedulePersistenceFactoryInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig;

class PriceProductScheduleEnableFinder implements PriceProductScheduleEnableFinderInterface
{
    protected const string COL_ID = 'id';

    protected const string COL_ROW_NUMBER = 'rn';

    protected const string COL_IS_CURRENT = 'is_current';

    protected const string PARAM_KEY_VALUE = 'value';

    protected const string DB_ENGINE_PGSQL = 'pgsql';

    public function __construct(
        protected PriceProductScheduleToPropelFacadeInterface $propelFacade,
        protected PriceProductSchedulePersistenceFactoryInterface $priceProductSchedulePersistenceFactory,
        protected PriceProductScheduleConfig $priceProductScheduleConfig,
        protected PriceProductScheduleMapperInterface $priceProductScheduleMapper,
        protected ConnectionInterface $databaseConnection
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStore(StoreTransfer $storeTransfer): array
    {
        $priceProductScheduleIds = $this->getPriceProductScheduleIds($storeTransfer->getIdStoreOrFail());

        return $this->findPriceProductSchedulesToEnableByPriceProductScheduleIds($priceProductScheduleIds);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStoreAndIdProductAbstract(
        StoreTransfer $storeTransfer,
        int $idProductAbstract
    ): array {
        $priceProductScheduleIds = $this->getPriceProductScheduleIds(
            $storeTransfer->getIdStoreOrFail(),
            $idProductAbstract,
        );

        return $this->findPriceProductSchedulesToEnableByPriceProductScheduleIds($priceProductScheduleIds);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStoreAndIdProductConcrete(
        StoreTransfer $storeTransfer,
        int $idProductConcrete
    ): array {
        $priceProductScheduleIds = $this->getPriceProductScheduleIds(
            $storeTransfer->getIdStoreOrFail(),
            null,
            $idProductConcrete,
        );

        return $this->findPriceProductSchedulesToEnableByPriceProductScheduleIds($priceProductScheduleIds);
    }

    /**
     * @param array<int> $priceProductScheduleIds
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    protected function findPriceProductSchedulesToEnableByPriceProductScheduleIds(array $priceProductScheduleIds): array
    {
        $priceProductScheduleEntities = $this->priceProductSchedulePersistenceFactory->createPriceProductScheduleQuery()
            ->joinWithCurrency()
            ->joinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->filterByIdPriceProductSchedule_In($priceProductScheduleIds)
            ->find()
            ->getData();

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers($priceProductScheduleEntities);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $limit
     *
     * @return \Generator<array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>>
     */
    public function getPriceProductSchedulesToEnableByStoreGenerator(StoreTransfer $storeTransfer, int $limit): Generator
    {
        while (true) {
            $priceProductScheduleIds = $this->getPriceProductScheduleIds(
                $storeTransfer->getIdStoreOrFail(),
                null,
                null,
                $limit,
            );

            if ($priceProductScheduleIds === []) {
                return;
            }

            yield $this->findPriceProductSchedulesToEnableByPriceProductScheduleIds($priceProductScheduleIds);
        }
    }

    /**
     * @param int $idStore
     * @param int|null $idProductAbstract
     * @param int|null $idProduct
     * @param int|null $limit
     *
     * @return array<int>
     */
    protected function getPriceProductScheduleIds(
        int $idStore,
        ?int $idProductAbstract = null,
        ?int $idProduct = null,
        ?int $limit = null,
    ): array {
        $limit = (int)($limit ?? $this->priceProductScheduleConfig->getApplyBatchSize());

        if ($this->propelFacade->getCurrentDatabaseEngine() === static::DB_ENGINE_PGSQL) {
            return $this->getPriceProductScheduleIdsWithRowNumber($idStore, $idProductAbstract, $idProduct, $limit);
        }

        return $this->getPriceProductScheduleIdsWithNotExists($idStore, $idProductAbstract, $idProduct, $limit);
    }

    /**
     * @return array<int>
     */
    protected function getPriceProductScheduleIdsWithRowNumber(
        int $idStore,
        ?int $idProductAbstract,
        ?int $idProduct,
        int $limit,
    ): array {
        $rowNumberExpression = sprintf(
            'ROW_NUMBER() OVER (PARTITION BY %s, %s, %s, %s ORDER BY %s ASC, %s ASC, %s ASC, %s ASC)',
            SpyPriceProductScheduleTableMap::COL_FK_PRICE_TYPE,
            SpyPriceProductScheduleTableMap::COL_FK_CURRENCY,
            SpyPriceProductScheduleTableMap::COL_FK_PRODUCT,
            SpyPriceProductScheduleTableMap::COL_FK_PRODUCT_ABSTRACT,
            SpyPriceProductScheduleTableMap::COL_ACTIVE_TO,
            SpyPriceProductScheduleTableMap::COL_GROSS_PRICE,
            SpyPriceProductScheduleTableMap::COL_NET_PRICE,
            SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE,
        );

        $priceProductScheduleQuery = $this->priceProductSchedulePersistenceFactory->createPriceProductScheduleQuery();
        $priceProductScheduleQuery
            ->addAsColumn(static::COL_ROW_NUMBER, $rowNumberExpression)
            ->addAsColumn(static::COL_ID, SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE)
            ->addAsColumn(static::COL_IS_CURRENT, SpyPriceProductScheduleTableMap::COL_IS_CURRENT)
            ->select([static::COL_ROW_NUMBER, static::COL_ID, static::COL_IS_CURRENT])
            ->filterByFkStore($idStore)
            ->joinPriceProductScheduleList()
            ->where(sprintf('%s = ?', SpyPriceProductScheduleListTableMap::COL_IS_ACTIVE), true)
            ->where(sprintf('%s <= NOW()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM))
            ->where(sprintf('%s >= NOW()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO));

        if ($idProductAbstract !== null) {
            $priceProductScheduleQuery->filterByFkProductAbstract($idProductAbstract);
        }

        if ($idProduct !== null) {
            $priceProductScheduleQuery->filterByFkProduct($idProduct);
        }

        $params = [];
        $innerSql = $priceProductScheduleQuery->createSelectSql($params);

        $outerSql = sprintf(
            'SELECT ranked.%s FROM (%s) ranked WHERE ranked.%s = 1 AND ranked.%s <> TRUE ORDER BY ranked.%s LIMIT %d',
            static::COL_ID,
            $innerSql,
            static::COL_ROW_NUMBER,
            static::COL_IS_CURRENT,
            static::COL_ID,
            $limit,
        );

        /** @var \Propel\Runtime\Connection\StatementInterface $statement */
        $statement = $this->databaseConnection->prepare($outerSql);

        foreach ($params as $index => $param) {
            $statement->bindValue(sprintf(':p%d', $index + 1), $param[static::PARAM_KEY_VALUE]);
        }

        $statement->execute();

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * @return array<int>
     */
    protected function getPriceProductScheduleIdsWithNotExists(
        int $idStore,
        ?int $idProductAbstract,
        ?int $idProduct,
        int $limit,
    ): array {
        /** @var literal-string $notExistsCondition */
        $notExistsCondition = $this->buildNotExistsCondition();

        $priceProductScheduleQuery = $this->priceProductSchedulePersistenceFactory->createPriceProductScheduleQuery();
        $priceProductScheduleQuery
            ->addAsColumn(static::COL_ID, SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE)
            ->select([static::COL_ID])
            ->filterByFkStore($idStore)
            ->joinPriceProductScheduleList()
            ->where(sprintf('%s = ?', SpyPriceProductScheduleListTableMap::COL_IS_ACTIVE), true)
            ->where(sprintf('%s <= NOW()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM))
            ->where(sprintf('%s >= NOW()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO))
            ->where($notExistsCondition)
            ->where(sprintf('%s = FALSE', SpyPriceProductScheduleTableMap::COL_IS_CURRENT))
            ->orderByIdPriceProductSchedule()
            ->setLimit($limit);

        if ($idProductAbstract !== null) {
            $priceProductScheduleQuery->filterByFkProductAbstract($idProductAbstract);
        }

        if ($idProduct !== null) {
            $priceProductScheduleQuery->filterByFkProduct($idProduct);
        }

        $params = [];
        $sql = $priceProductScheduleQuery->createSelectSql($params);

        /** @var \Propel\Runtime\Connection\StatementInterface $statement */
        $statement = $this->databaseConnection->prepare($sql);

        foreach ($params as $index => $param) {
            $statement->bindValue(sprintf(':p%d', $index + 1), $param[static::PARAM_KEY_VALUE]);
        }

        $statement->execute();

        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    protected function buildNotExistsCondition(): string
    {
        $colActiveTo = SpyPriceProductScheduleTableMap::COL_ACTIVE_TO;
        $colGrossPrice = SpyPriceProductScheduleTableMap::COL_GROSS_PRICE;
        $colNetPrice = SpyPriceProductScheduleTableMap::COL_NET_PRICE;
        $colId = SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE;

        return sprintf(
            'NOT EXISTS (
                SELECT 1
                FROM %s AS s2
                JOIN %s AS l2
                  ON l2.id_price_product_schedule_list = s2.fk_price_product_schedule_list
                 AND l2.is_active = TRUE
                WHERE s2.fk_store = %s
                  AND s2.active_from <= NOW()
                  AND s2.active_to >= NOW()
                  AND s2.fk_price_type = %s
                  AND s2.fk_currency = %s
                  AND s2.fk_product <=> %s
                  AND s2.fk_product_abstract <=> %s
                  AND (
                       s2.active_to,
                       COALESCE(s2.gross_price, -2147483648),
                       COALESCE(s2.net_price, -2147483648),
                       s2.id_price_product_schedule
                  ) < (
                       %s,
                       COALESCE(%s, -2147483648),
                       COALESCE(%s, -2147483648),
                       %s
                  )
            )',
            SpyPriceProductScheduleTableMap::TABLE_NAME,
            SpyPriceProductScheduleListTableMap::TABLE_NAME,
            SpyPriceProductScheduleTableMap::COL_FK_STORE,
            SpyPriceProductScheduleTableMap::COL_FK_PRICE_TYPE,
            SpyPriceProductScheduleTableMap::COL_FK_CURRENCY,
            SpyPriceProductScheduleTableMap::COL_FK_PRODUCT,
            SpyPriceProductScheduleTableMap::COL_FK_PRODUCT_ABSTRACT,
            $colActiveTo,
            $colGrossPrice,
            $colNetPrice,
            $colId,
        );
    }
}
