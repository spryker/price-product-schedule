<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence;

use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleListQuery;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPropelFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Disable\PriceProductScheduleDisableFinder;
use Spryker\Zed\PriceProductSchedule\Persistence\Disable\PriceProductScheduleDisableFinderInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Enable\PriceProductScheduleEnableFinder;
use Spryker\Zed\PriceProductSchedule\Persistence\Enable\PriceProductScheduleEnableFinderInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Finder\PriceProductScheduleFinder;
use Spryker\Zed\PriceProductSchedule\Persistence\Finder\PriceProductScheduleFinderInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Finder\PriceProductScheduleListFinder;
use Spryker\Zed\PriceProductSchedule\Persistence\Finder\PriceProductScheduleListFinderInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleListMapper;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleListMapperInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapper;
use Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper\PriceProductScheduleMapperInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleDependencyProvider;

/**
 * @method \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig getConfig()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface getRepository()
 */
class PriceProductSchedulePersistenceFactory extends AbstractPersistenceFactory implements PriceProductSchedulePersistenceFactoryInterface
{
    public function createPriceProductScheduleQuery(): SpyPriceProductScheduleQuery
    {
        return SpyPriceProductScheduleQuery::create();
    }

    public function createPriceProductScheduleListQuery(): SpyPriceProductScheduleListQuery
    {
        return SpyPriceProductScheduleListQuery::create();
    }

    public function createPriceProductScheduleMapper(): PriceProductScheduleMapperInterface
    {
        return new PriceProductScheduleMapper(
            $this->createPriceProductScheduleListMapper(),
            $this->getConfig(),
        );
    }

    public function createPriceProductScheduleListMapper(): PriceProductScheduleListMapperInterface
    {
        return new PriceProductScheduleListMapper();
    }

    public function createPriceProductScheduleDisableFinder(): PriceProductScheduleDisableFinderInterface
    {
        return new PriceProductScheduleDisableFinder(
            $this->createPriceProductScheduleQuery(),
            $this->createPriceProductScheduleMapper(),
        );
    }

    public function createPriceProductScheduleEnableFinder(): PriceProductScheduleEnableFinderInterface
    {
        return new PriceProductScheduleEnableFinder(
            $this->getPropelFacade(),
            $this,
            $this->getConfig(),
            $this->createPriceProductScheduleMapper(),
            $this->getDatabaseConnection(),
        );
    }

    public function createPriceProductScheduleFinder(): PriceProductScheduleFinderInterface
    {
        return new PriceProductScheduleFinder(
            $this->createPriceProductScheduleQuery(),
            $this->createPriceProductScheduleMapper(),
        );
    }

    public function createPriceProductScheduleListFinder(): PriceProductScheduleListFinderInterface
    {
        return new PriceProductScheduleListFinder(
            $this->createPriceProductScheduleListQuery(),
            $this->createPriceProductScheduleListMapper(),
        );
    }

    public function getPropelFacade(): PriceProductScheduleToPropelFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_PROPEL);
    }

    public function getDatabaseConnection(): ConnectionInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::CONNECTION_DATABASE);
    }
}
