<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\PriceProductScheduledApplyRequestTransfer;
use Generated\Shared\Transfer\PriceProductScheduledApplyResponseTransfer;

interface PriceProductScheduleApplierInterface
{
    public function applyScheduledPrices(?string $storeName = null): void;

    public function applyAllScheduledPrices(
        PriceProductScheduledApplyRequestTransfer $priceProductScheduledApplyRequestTransfer
    ): PriceProductScheduledApplyResponseTransfer;
}
