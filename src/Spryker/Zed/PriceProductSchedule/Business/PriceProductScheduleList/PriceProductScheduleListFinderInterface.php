<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList;

use Generated\Shared\Transfer\PriceProductScheduleListResponseTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListTransfer;

interface PriceProductScheduleListFinderInterface
{
    public function findPriceProductScheduleList(
        PriceProductScheduleListTransfer $requestedPriceProductScheduleListTransfer
    ): PriceProductScheduleListResponseTransfer;

    public function findDefaultPriceProductScheduleList(): ?PriceProductScheduleListTransfer;
}
