<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\PriceProductScheduleImportTransfer;
use Generated\Shared\Transfer\PriceProductScheduleTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\StoreTransfer;

class PriceProductScheduleMapper implements PriceProductScheduleMapperInterface
{
    public function mapPriceProductScheduleImportTransferToPriceProductScheduleTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer,
        PriceProductScheduleTransfer $priceProductScheduleTransfer
    ): PriceProductScheduleTransfer {
        $priceProductTransfer = $this->mapPriceProductScheduleImportTransferToPriceProductTransfer(
            $priceProductScheduleImportTransfer,
            $this->createPriceProductTransfer(),
        );

        return $priceProductScheduleTransfer
            ->fromArray($priceProductScheduleImportTransfer->toArray(), true)
            ->setPriceProduct($priceProductTransfer);
    }

    protected function mapPriceProductScheduleImportTransferToPriceProductTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer,
        PriceProductTransfer $priceProductTransfer
    ): PriceProductTransfer {
        $moneyValueTransfer = $this->mapMoneyValueTransferFromPriceProductScheduleImportTransfer(
            $priceProductScheduleImportTransfer,
            $this->createMoneyValueTransfer(),
        );

        return $priceProductTransfer
            ->fromArray($priceProductScheduleImportTransfer->toArray(), true)
            ->setMoneyValue($moneyValueTransfer);
    }

    protected function mapMoneyValueTransferFromPriceProductScheduleImportTransfer(
        PriceProductScheduleImportTransfer $priceProductScheduleImportTransfer,
        MoneyValueTransfer $moneyValueTransfer
    ): MoneyValueTransfer {
        $currencyTransfer = $this->createCurrencyTransfer($priceProductScheduleImportTransfer->getCurrencyCode());
        $storeTransfer = $this->createStoreTransfer($priceProductScheduleImportTransfer->getStoreName());

        return $moneyValueTransfer
            ->setCurrency($currencyTransfer)
            ->setStore($storeTransfer)
            ->setNetAmount($priceProductScheduleImportTransfer->getNetAmount())
            ->setGrossAmount($priceProductScheduleImportTransfer->getGrossAmount());
    }

    protected function createPriceProductTransfer(): PriceProductTransfer
    {
        return new PriceProductTransfer();
    }

    protected function createMoneyValueTransfer(): MoneyValueTransfer
    {
        return new MoneyValueTransfer();
    }

    protected function createCurrencyTransfer(string $code): CurrencyTransfer
    {
        return (new CurrencyTransfer())->setCode($code);
    }

    protected function createStoreTransfer(string $name): StoreTransfer
    {
        return (new StoreTransfer())->setName($name);
    }
}
