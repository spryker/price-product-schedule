<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence;

use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleExportItemCollectionTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generator;

interface PriceProductScheduleRepositoryInterface
{
    /**
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisable(): array;

    public function isScheduledPriceForSwitchExists(PriceProductScheduleTransfer $priceProductScheduleTransfer): bool;

    /**
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisableByIdProductAbstract(int $idProductAbstract): array;

    /**
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToDisableByIdProductConcrete(int $idProductConcrete): array;

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer $priceProductScheduleTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findSimilarPriceProductSchedulesToDisable(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): array;

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $limit
     *
     * @return \Generator<array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>>
     */
    public function getPriceProductSchedulesToEnableByStoreGenerator(StoreTransfer $storeTransfer, int $limit): Generator;

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStore(StoreTransfer $storeTransfer): array;

    public function findCountPriceProductScheduleByCriteriaFilter(
        PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
    ): int;

    public function findPriceProductScheduleListById(
        PriceProductScheduleListTransfer $priceProductScheduleListTransfer
    ): ?PriceProductScheduleListTransfer;

    /**
     * @param int $idPriceProductScheduleList
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesByIdPriceProductScheduleList(
        int $idPriceProductScheduleList
    ): array;

    public function findPriceProductScheduleById(
        int $idPriceProductSchedule
    ): ?PriceProductScheduleTransfer;

    public function findPriceProductScheduleListByName(string $name): ?PriceProductScheduleListTransfer;

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $idProductAbstract
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStoreAndIdProductAbstract(
        StoreTransfer $storeTransfer,
        int $idProductAbstract
    ): array;

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function findPriceProductSchedulesToEnableByStoreAndIdProductConcrete(
        StoreTransfer $storeTransfer,
        int $idProductConcrete
    ): array;

    public function isPriceProductScheduleUnique(PriceProductScheduleTransfer $priceProductScheduleTransfer): bool;

    public function findPriceProductScheduleExportItemsByIdPriceProductScheduleList(
        int $idPriceProductScheduleList,
        int $lastProcessedId,
        int $limit
    ): PriceProductScheduleExportItemCollectionTransfer;
}
