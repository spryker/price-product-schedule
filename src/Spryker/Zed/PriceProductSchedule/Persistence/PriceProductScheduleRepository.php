<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleExportItemCollectionTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generator;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductSchedulePersistenceFactory getFactory()
 */
class PriceProductScheduleRepository extends AbstractRepository implements PriceProductScheduleRepositoryInterface
{
    /**
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisable(): array
    {
        return $this->getFactory()
            ->createPriceProductScheduleDisableFinder()
            ->findPriceProductSchedulesToDisable();
    }

    public function isScheduledPriceForSwitchExists(PriceProductScheduleTransfer $priceProductScheduleTransfer): bool
    {
        return $this->getFactory()
            ->createPriceProductScheduleDisableFinder()
            ->isScheduledPriceForSwitchExists($priceProductScheduleTransfer);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisableByIdProductAbstract(int $idProductAbstract): array
    {
        return $this->getFactory()
            ->createPriceProductScheduleDisableFinder()
            ->findPriceProductSchedulesToDisableByIdProductAbstract($idProductAbstract);
    }

    /**
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisableByIdProductConcrete(int $idProductConcrete): array
    {
        return $this->getFactory()
            ->createPriceProductScheduleDisableFinder()
            ->findPriceProductSchedulesToDisableByIdProductConcrete($idProductConcrete);
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer $priceProductScheduleTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findSimilarPriceProductSchedulesToDisable(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): array {
        return $this->getFactory()
            ->createPriceProductScheduleDisableFinder()
            ->findSimilarPriceProductSchedulesToDisable($priceProductScheduleTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $limit
     *
     * @return \Generator<array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>>
     */
    public function getPriceProductSchedulesToEnableByStoreGenerator(StoreTransfer $storeTransfer, int $limit): Generator
    {
        return $this->getFactory()
           ->createPriceProductScheduleEnableFinder()
           ->getPriceProductSchedulesToEnableByStoreGenerator($storeTransfer, $limit);
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStore(StoreTransfer $storeTransfer): array
    {
        return $this->getFactory()
            ->createPriceProductScheduleEnableFinder()
            ->findPriceProductSchedulesToEnableByStore($storeTransfer);
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
        return $this->getFactory()
            ->createPriceProductScheduleEnableFinder()
            ->findPriceProductSchedulesToEnableByStoreAndIdProductAbstract($storeTransfer, $idProductAbstract);
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
        return $this->getFactory()
            ->createPriceProductScheduleEnableFinder()
            ->findPriceProductSchedulesToEnableByStoreAndIdProductConcrete($storeTransfer, $idProductConcrete);
    }

    public function findCountPriceProductScheduleByCriteriaFilter(
        PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
    ): int {
        return $this->getFactory()
            ->createPriceProductScheduleFinder()
            ->findCountPriceProductScheduleByCriteriaFilter($priceProductScheduleCriteriaFilterTransfer);
    }

    public function findPriceProductScheduleListById(
        PriceProductScheduleListTransfer $priceProductScheduleListTransfer
    ): ?PriceProductScheduleListTransfer {
        return $this->getFactory()
            ->createPriceProductScheduleListFinder()
            ->findPriceProductScheduleListById($priceProductScheduleListTransfer);
    }

    public function findPriceProductScheduleById(int $idPriceProductSchedule): ?PriceProductScheduleTransfer
    {
        return $this->getFactory()
            ->createPriceProductScheduleFinder()
            ->findPriceProductScheduleById($idPriceProductSchedule);
    }

    public function findPriceProductScheduleListByName(string $name): ?PriceProductScheduleListTransfer
    {
        return $this->getFactory()
            ->createPriceProductScheduleListFinder()
            ->findPriceProductScheduleListByName($name);
    }

    public function isPriceProductScheduleUnique(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): bool {
        return $this->getFactory()
            ->createPriceProductScheduleFinder()
            ->isPriceProductScheduleUnique($priceProductScheduleTransfer);
    }

    /**
     * @param int $idPriceProductScheduleList
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesByIdPriceProductScheduleList(
        int $idPriceProductScheduleList
    ): array {
        return $this->getFactory()
            ->createPriceProductScheduleFinder()
            ->findPriceProductSchedulesByIdPriceProductScheduleList($idPriceProductScheduleList);
    }

    public function findPriceProductScheduleExportItemsByIdPriceProductScheduleList(
        int $idPriceProductScheduleList,
        int $lastProcessedId,
        int $limit
    ): PriceProductScheduleExportItemCollectionTransfer {
        $exportItems = $this->getFactory()
            ->createPriceProductScheduleFinder()
            ->findPriceProductScheduleExportItemsByIdPriceProductScheduleList(
                $idPriceProductScheduleList,
                $lastProcessedId,
                $limit,
            );

        return (new PriceProductScheduleExportItemCollectionTransfer())
            ->setPriceProductScheduleExportItems(new ArrayObject($exportItems));
    }
}
