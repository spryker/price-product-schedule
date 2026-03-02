<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleImportTransfer;

interface PriceProductScheduleImportMapperInterface
{
    public function mapPriceProductScheduleImportTransferToPriceProductScheduleCriteriaFilterTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer,
        PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
    ): PriceProductScheduleCriteriaFilterTransfer;

    public function mapPriceProductScheduleRowToPriceProductScheduleImportTransfer(
        array $importData,
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer
    ): PriceProductScheduleImportTransfer;
}
