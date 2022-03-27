<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Enable;

use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\PriceProductSchedule\Persistence\Map\SpyPriceProductScheduleTableMap;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPropelFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Exception\NotSupportedDbEngineException;
use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductSchedulePersistenceFactoryInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig;
use Spryker\Zed\Propel\PropelConfig;

class PriceProductScheduleEnableFinder implements PriceProductScheduleEnableFinderInterface
{
    /**
     * @var string
     */
    protected const COL_PRODUCT_ID = 'product_id';

    /**
     * @var string
     */
    protected const COL_RESULT = 'result';

    /**
     * @var string
     */
    protected const ALIAS_CONCATENATED = 'concatenated';

    /**
     * @var string
     */
    protected const ALIAS_FILTERED = 'filtered';

    /**
     * @var string
     */
    protected const MESSAGE_NOT_SUPPORTED_DB_ENGINE = 'DB engine "%s" is not supported. Please extend EXPRESSION_CONCATENATED_RESULT_MAP';

    /**
     * @var array<string, string>
     */
    protected const EXPRESSION_CONCATENATED_RESULT_MAP = [
        PropelConfig::DB_ENGINE_PGSQL => 'CAST(CONCAT(CONCAT(CAST(EXTRACT(epoch from now() - %s) + EXTRACT(epoch from %s - now()) AS INT), \'.\'), %s + %s) as DECIMAL)',
        PropelConfig::DB_ENGINE_MYSQL => 'CONCAT(CONCAT(CAST(TIMESTAMPDIFF(minute, %s, now()) + TIMESTAMPDIFF(minute, now(), %s) AS BINARY), \'.\'), %s + %s) + 0',
    ];

    /**
     * @var string
     */
    protected const EXPRESSION_CONCATENATED_PRODUCT_ID = 'CONCAT(%s, \' \', %s, \' \', COALESCE(%s, 0), \'_\', COALESCE(%s, 0))';

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPropelFacadeInterface
     */
    protected $propelFacade;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductSchedulePersistenceFactoryInterface
     */
    protected $factory;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig
     */
    protected $config;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface
     */
    protected $priceProductScheduleMapper;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPropelFacadeInterface $propelFacade
     * @param \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductSchedulePersistenceFactoryInterface $factory
     * @param \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig $config
     * @param \Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface $priceProductScheduleMapper
     */
    public function __construct(
        PriceProductScheduleToPropelFacadeInterface $propelFacade,
        PriceProductSchedulePersistenceFactoryInterface $factory,
        PriceProductScheduleConfig $config,
        PriceProductScheduleMapperInterface $priceProductScheduleMapper
    ) {
        $this->propelFacade = $propelFacade;
        $this->factory = $factory;
        $this->config = $config;
        $this->priceProductScheduleMapper = $priceProductScheduleMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStore(StoreTransfer $storeTransfer): array
    {
        $currentDatabaseEngineName = $this->propelFacade->getCurrentDatabaseEngine();
        $priceProductScheduleFilteredByMinResultSubQuery = $this->createPriceProductScheduleFilteredByMinResultSubQuery(
            $storeTransfer,
            $currentDatabaseEngineName,
        );

        return $this->findPriceProductSchedulesToEnableByStoreResult(
            $priceProductScheduleFilteredByMinResultSubQuery,
            $storeTransfer,
            $currentDatabaseEngineName,
        );
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
        $currentDatabaseEngineName = $this->propelFacade->getCurrentDatabaseEngine();
        $priceProductScheduleFilteredByMinResultSubQuery = $this->createPriceProductScheduleFilteredByMinResultSubQuery(
            $storeTransfer,
            $currentDatabaseEngineName,
        );

        return $this->findPriceProductSchedulesToEnableByStoreAndIdProductAbstractResult(
            $priceProductScheduleFilteredByMinResultSubQuery,
            $storeTransfer,
            $currentDatabaseEngineName,
            $idProductAbstract,
        );
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
        $currentDatabaseEngineName = $this->propelFacade->getCurrentDatabaseEngine();
        $priceProductScheduleFilteredByMinResultSubQuery = $this->createPriceProductScheduleFilteredByMinResultSubQuery(
            $storeTransfer,
            $currentDatabaseEngineName,
        );

        return $this->findPriceProductSchedulesToEnableByStoreAndIdProductConcreteResult(
            $priceProductScheduleFilteredByMinResultSubQuery,
            $storeTransfer,
            $currentDatabaseEngineName,
            $idProductConcrete,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param string $currentDatabaseEngineName
     *
     * @return \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery
     */
    protected function createPriceProductScheduleFilteredByMinResultSubQuery(
        StoreTransfer $storeTransfer,
        string $currentDatabaseEngineName
    ): SpyPriceProductScheduleQuery {
        $priceProductScheduleConcatenatedSubQuery = $this->createPriceProductScheduleConcatenatedSubQuery(
            $currentDatabaseEngineName,
        );

        $priceProductScheduleConcatenatedSubQuery = $this->setConditionsForPriceProductScheduleQuery(
            $priceProductScheduleConcatenatedSubQuery,
            $storeTransfer,
        );

        $priceProductScheduleFilteredByMinResultSubQuery = $this->factory->createPriceProductScheduleQuery()
            ->addSelectQuery($priceProductScheduleConcatenatedSubQuery, static::ALIAS_CONCATENATED, false)
            ->addAsColumn(static::COL_PRODUCT_ID, static::ALIAS_CONCATENATED . '.' . static::COL_PRODUCT_ID)
            ->addAsColumn(static::COL_RESULT, sprintf('min(%s)', static::ALIAS_CONCATENATED . '.' . static::COL_RESULT));

        return $this->setConditionsForPriceProductScheduleQuery(
            $priceProductScheduleFilteredByMinResultSubQuery,
            $storeTransfer,
        )->groupBy(static::COL_PRODUCT_ID);
    }

    /**
     * @param \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $priceProductScheduleQuery
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery
     */
    protected function setConditionsForPriceProductScheduleQuery(
        SpyPriceProductScheduleQuery $priceProductScheduleQuery,
        StoreTransfer $storeTransfer
    ): SpyPriceProductScheduleQuery {
        /** @var literal-string $activeFromCondition */
        $activeFromCondition = sprintf('%s <= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM);
        /** @var literal-string $activeToCondition */
        $activeToCondition = sprintf('%s >= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO);

        /** @phpstan-var \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery */
        return $priceProductScheduleQuery
            ->filterByFkStore($storeTransfer->getIdStore())
            ->where($activeFromCondition)
            ->where($activeToCondition)
            ->usePriceProductScheduleListQuery()
                ->filterByIsActive(true)
            ->endUse();
    }

    /**
     * @param string $currentDatabaseEngineName
     *
     * @return \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery
     */
    protected function createPriceProductScheduleConcatenatedSubQuery(
        string $currentDatabaseEngineName
    ): SpyPriceProductScheduleQuery {
        $concatenatedResultExpression = $this->getConcatenatedResultExpressionByDbEngineName($currentDatabaseEngineName);

        return $this->factory->createPriceProductScheduleQuery()
            ->select([static::COL_PRODUCT_ID])
            ->addAsColumn(
                static::COL_PRODUCT_ID,
                sprintf(
                    static::EXPRESSION_CONCATENATED_PRODUCT_ID,
                    SpyPriceProductScheduleTableMap::COL_FK_PRICE_TYPE,
                    SpyPriceProductScheduleTableMap::COL_FK_CURRENCY,
                    SpyPriceProductScheduleTableMap::COL_FK_PRODUCT,
                    SpyPriceProductScheduleTableMap::COL_FK_PRODUCT_ABSTRACT,
                ),
            )
            ->addAsColumn(
                static::COL_RESULT,
                sprintf(
                    $concatenatedResultExpression,
                    SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM,
                    SpyPriceProductScheduleTableMap::COL_ACTIVE_TO,
                    SpyPriceProductScheduleTableMap::COL_NET_PRICE,
                    SpyPriceProductScheduleTableMap::COL_GROSS_PRICE,
                    SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE,
                ),
            );
    }

    /**
     * @param string $databaseEngineName
     *
     * @throws \Spryker\Zed\PriceProductSchedule\Persistence\Exception\NotSupportedDbEngineException
     *
     * @return string
     */
    protected function getConcatenatedResultExpressionByDbEngineName(string $databaseEngineName): string
    {
        if (isset(static::EXPRESSION_CONCATENATED_RESULT_MAP[$databaseEngineName]) === false) {
            throw new NotSupportedDbEngineException(
                sprintf(static::MESSAGE_NOT_SUPPORTED_DB_ENGINE, $databaseEngineName),
            );
        }

        return static::EXPRESSION_CONCATENATED_RESULT_MAP[$databaseEngineName];
    }

    /**
     * @module Product
     * @module PriceProduct
     * @module Currency
     *
     * @param \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $subQuery
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param string $dbEngineName
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    protected function findPriceProductSchedulesToEnableByStoreResult(
        SpyPriceProductScheduleQuery $subQuery,
        StoreTransfer $storeTransfer,
        string $dbEngineName
    ): array {
        /** @var literal-string $activeFromCondition */
        $activeFromCondition = sprintf('%s <= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM);
        /** @var literal-string $activeToCondition */
        $activeToCondition = sprintf('%s >= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO);

        /** @var literal-string $filterByConcatenatedProductIdExpression */
        $filterByConcatenatedProductIdExpression = $this->getFilterByConcatenatedProductIdExpression();
        /** @var literal-string $filterByConcatenatedResultExpression */
        $filterByConcatenatedResultExpression = $this->getFilterByConcatenatedResultExpression($dbEngineName);

        $priceProductScheduleEntities = $this->factory->createPriceProductScheduleQuery()
            ->addSelectQuery($subQuery, static::ALIAS_FILTERED, false)
            ->joinWithCurrency()
            ->joinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->filterByIsCurrent(false)
            ->filterByFkStore($storeTransfer->getIdStore())
            ->where($filterByConcatenatedProductIdExpression)
            ->where($filterByConcatenatedResultExpression)
            ->where($activeFromCondition)
            ->where($activeToCondition)
            ->limit($this->config->getApplyBatchSize())
            ->find()
            ->getData();

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers($priceProductScheduleEntities);
    }

    /**
     * @return string
     */
    protected function getFilterByConcatenatedProductIdExpression(): string
    {
        return sprintf(
            '(%s) = %s',
            sprintf(
                static::EXPRESSION_CONCATENATED_PRODUCT_ID,
                SpyPriceProductScheduleTableMap::COL_FK_PRICE_TYPE,
                SpyPriceProductScheduleTableMap::COL_FK_CURRENCY,
                SpyPriceProductScheduleTableMap::COL_FK_PRODUCT,
                SpyPriceProductScheduleTableMap::COL_FK_PRODUCT_ABSTRACT,
            ),
            static::ALIAS_FILTERED . '.' . static::COL_PRODUCT_ID,
        );
    }

    /**
     * @param string $databaseEngineName
     *
     * @return string
     */
    protected function getFilterByConcatenatedResultExpression(string $databaseEngineName): string
    {
        $concatenatedResultExpression = $this->getConcatenatedResultExpressionByDbEngineName($databaseEngineName);

        return sprintf(
            '(%s) = %s',
            sprintf(
                $concatenatedResultExpression,
                SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM,
                SpyPriceProductScheduleTableMap::COL_ACTIVE_TO,
                SpyPriceProductScheduleTableMap::COL_NET_PRICE,
                SpyPriceProductScheduleTableMap::COL_GROSS_PRICE,
                SpyPriceProductScheduleTableMap::COL_ID_PRICE_PRODUCT_SCHEDULE,
            ),
            static::ALIAS_FILTERED . '.' . static::COL_RESULT,
        );
    }

    /**
     * @module Product
     * @module PriceProduct
     * @module Currency
     *
     * @param \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $subQuery
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param string $dbEngineName
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    protected function findPriceProductSchedulesToEnableByStoreAndIdProductAbstractResult(
        SpyPriceProductScheduleQuery $subQuery,
        StoreTransfer $storeTransfer,
        string $dbEngineName,
        int $idProductAbstract
    ): array {
        /** @var literal-string $activeFromCondition */
        $activeFromCondition = sprintf('%s <= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM);
        /** @var literal-string $activeToCondition */
        $activeToCondition = sprintf('%s >= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO);

        /** @var literal-string $filterByConcatenatedProductIdExpression */
        $filterByConcatenatedProductIdExpression = $this->getFilterByConcatenatedProductIdExpression();
        /** @var literal-string $filterByConcatenatedResultExpression */
        $filterByConcatenatedResultExpression = $this->getFilterByConcatenatedResultExpression($dbEngineName);

        $priceProductScheduleEntities = $this->factory->createPriceProductScheduleQuery()
            ->addSelectQuery($subQuery, static::ALIAS_FILTERED, false)
            ->joinWithCurrency()
            ->joinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->filterByIsCurrent(false)
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterByFkProductAbstract($idProductAbstract)
            ->where($filterByConcatenatedProductIdExpression)
            ->where($filterByConcatenatedResultExpression)
            ->where($activeFromCondition)
            ->where($activeToCondition)
            ->find()
            ->getData();

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers($priceProductScheduleEntities);
    }

    /**
     * @module Product
     * @module PriceProduct
     * @module Currency
     *
     * @param \Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery $subQuery
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param string $dbEngineName
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    protected function findPriceProductSchedulesToEnableByStoreAndIdProductConcreteResult(
        SpyPriceProductScheduleQuery $subQuery,
        StoreTransfer $storeTransfer,
        string $dbEngineName,
        int $idProductConcrete
    ): array {
        /** @var literal-string $activeFromCondition */
        $activeFromCondition = sprintf('%s <= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_FROM);
        /** @var literal-string $activeToCondition */
        $activeToCondition = sprintf('%s >= now()', SpyPriceProductScheduleTableMap::COL_ACTIVE_TO);

        /** @var literal-string $filterByConcatenatedProductIdExpression */
        $filterByConcatenatedProductIdExpression = $this->getFilterByConcatenatedProductIdExpression();
        /** @var literal-string $filterByConcatenatedResultExpression */
        $filterByConcatenatedResultExpression = $this->getFilterByConcatenatedResultExpression($dbEngineName);

        $priceProductScheduleEntities = $this->factory->createPriceProductScheduleQuery()
            ->addSelectQuery($subQuery, static::ALIAS_FILTERED, false)
            ->joinWithCurrency()
            ->joinWithPriceType()
            ->leftJoinWithProduct()
            ->leftJoinWithProductAbstract()
            ->filterByIsCurrent(false)
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterByFkProduct($idProductConcrete)
            ->where($filterByConcatenatedProductIdExpression)
            ->where($filterByConcatenatedResultExpression)
            ->where($activeFromCondition)
            ->where($activeToCondition)
            ->find()
            ->getData();

        return $this->priceProductScheduleMapper
            ->mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers($priceProductScheduleEntities);
    }
}
