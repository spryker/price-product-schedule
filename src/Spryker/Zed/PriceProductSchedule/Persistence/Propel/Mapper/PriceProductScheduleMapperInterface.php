<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductSchedule;

interface PriceProductScheduleMapperInterface
{
    public function mapPriceProductScheduleEntityToPriceProductScheduleTransfer(
        SpyPriceProductSchedule $priceProductScheduleEntity,
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): PriceProductScheduleTransfer;

    public function mapPriceProductScheduleTransferToPriceProductScheduleEntity(
        PriceProductScheduleTransfer $priceProductScheduleTransfer,
        SpyPriceProductSchedule $priceProductScheduleEntity
    ): SpyPriceProductSchedule;

    /**
     * @param array<\Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductSchedule> $priceProductScheduleEntities
     *
     * @return array<\Generated\Shared\Transfer\PriceProductScheduleTransfer>
     */
    public function mapPriceProductScheduleEntitiesToPriceProductScheduleTransfers(array $priceProductScheduleEntities): array;
}
