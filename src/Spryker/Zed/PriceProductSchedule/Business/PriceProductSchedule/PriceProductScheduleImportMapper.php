<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\PriceProductScheduleCriteriaFilterTransfer;
use Generated\Shared\Transfer\PriceProductScheduleImportMetaDataTransfer;
use Generated\Shared\Transfer\PriceProductScheduleImportTransfer;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig;

class PriceProductScheduleImportMapper implements PriceProductScheduleImportMapperInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig
     */
    protected $priceProductScheduleConfig;

    public function __construct(
        PriceProductScheduleConfig $priceProductScheduleConfig
    ) {
        $this->priceProductScheduleConfig = $priceProductScheduleConfig;
    }

    public function mapPriceProductScheduleImportTransferToPriceProductScheduleCriteriaFilterTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer,
        PriceProductScheduleCriteriaFilterTransfer $priceProductScheduleCriteriaFilterTransfer
    ): PriceProductScheduleCriteriaFilterTransfer {
        return $this->createPriceProductScheduleCriteriaFilterTransfer()
            ->fromArray($priceProductScheduleImportTransfer->toArray(), true);
    }

    public function mapPriceProductScheduleRowToPriceProductScheduleImportTransfer(
        array $importData,
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer
    ): PriceProductScheduleImportTransfer {
        $preparedImportData = [];
        $fieldsMap = $this->priceProductScheduleConfig->getImportFileToTransferFieldsMap();

        foreach ($importData as $key => $value) {
            $preparedImportData[$fieldsMap[$key]] = $value ?: null;
        }

        return $priceProductScheduleImportTransfer
            ->fromArray($preparedImportData)
            ->setMetaData(new PriceProductScheduleImportMetaDataTransfer());
    }

    protected function createPriceProductScheduleCriteriaFilterTransfer(): PriceProductScheduleCriteriaFilterTransfer
    {
        return new PriceProductScheduleCriteriaFilterTransfer();
    }
}
