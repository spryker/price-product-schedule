<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Finder;

use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;

interface PriceProductScheduleFinderInterface
{
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
    public function findCountPriceProductScheduleByCriteriaFilter(PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer): int;

    /**
     * @param int $idPriceProductSchedule
     *
     * @return \Generated\Shared\Transfer\PriceProductScheduleTransfer|null
     */
    public function findPriceProductScheduleById(int $idPriceProductSchedule): ?PriceProductScheduleTransfer;

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleTransfer $priceProductScheduleTransfer
     *
     * @return bool
     */
    public function isPriceProductScheduleUnique(PriceProductScheduleTransfer $priceProductScheduleTransfer): bool;

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
    ): array;
}
