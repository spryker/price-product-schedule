<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\PriceProductScheduleImportTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListImportErrorTransfer;

class PriceProductScheduleImportValidator implements PriceProductScheduleImportValidatorInterface
{
    /**
     * @var array<\Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\ImportDataValidatorInterface>
     */
    protected $dataValidatorList;

    /**
     * @param array<\Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\ImportDataValidatorInterface> $dataValidatorList
     */
    public function __construct(array $dataValidatorList = [])
    {
        $this->dataValidatorList = $dataValidatorList;
    }

    public function validatePriceProductScheduleImportTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer
    ): ?PriceProductScheduleListImportErrorTransfer {
        foreach ($this->dataValidatorList as $dataValidator) {
            $priceProductScheduleListImportError = $dataValidator
                ->validatePriceProductScheduleImportTransfer($priceProductScheduleImportTransfer);

            if ($priceProductScheduleListImportError !== null) {
                return $priceProductScheduleListImportError;
            }
        }

        return null;
    }
}
