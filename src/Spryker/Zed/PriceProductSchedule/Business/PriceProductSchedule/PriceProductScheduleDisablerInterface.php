<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\PriceProductScheduleTransfer;

interface PriceProductScheduleDisablerInterface
{
    public function disableNotActiveScheduledPrices(): void;

    public function disableNotActiveScheduledPricesByIdProductAbstract(int $idProductAbstract): void;

    public function disableNotActiveScheduledPricesByIdProductConcrete(int $idProductConcrete): void;

    public function disableNotRelevantPriceProductSchedulesByPriceProductSchedule(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): void;

    public function disablePriceProductSchedule(PriceProductScheduleTransfer $priceProductScheduleTransfer): PriceProductScheduleTransfer;

    public function deactivatePriceProductSchedule(
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): PriceProductScheduleTransfer;
}
