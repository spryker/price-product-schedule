<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander;

use Generated\Shared\Transfer\PriceProductTransfer;
use Spryker\Zed\PriceProductSchedule\Business\Currency\CurrencyFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\Store\StoreFinderInterface;

class PriceProductTransferMoneyValueDataExpander implements PriceProductTransferDataExpanderInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\Store\StoreFinderInterface
     */
    protected $priceProductScheduleStoreFinder;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\Currency\CurrencyFinderInterface
     */
    protected $priceProductScheduleCurrencyFinder;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Business\Store\StoreFinderInterface $priceProductScheduleStoreFinder
     * @param \Spryker\Zed\PriceProductSchedule\Business\Currency\CurrencyFinderInterface $priceProductScheduleCurrencyFinder
     */
    public function __construct(
        StoreFinderInterface $priceProductScheduleStoreFinder,
        CurrencyFinderInterface $priceProductScheduleCurrencyFinder
    ) {
        $this->priceProductScheduleStoreFinder = $priceProductScheduleStoreFinder;
        $this->priceProductScheduleCurrencyFinder = $priceProductScheduleCurrencyFinder;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer
     */
    public function expand(PriceProductTransfer $priceProductTransfer): PriceProductTransfer
    {
        $currencyTransfer = $this->priceProductScheduleCurrencyFinder
            ->findCurrencyByIsoCode($priceProductTransfer->getMoneyValue()->getCurrency()->getCode());

        if ($currencyTransfer !== null) {
            $priceProductTransfer->getMoneyValue()
                ->setCurrency($currencyTransfer)
                ->setFkCurrency($currencyTransfer->getIdCurrency());
        }

        $storeTransfer = $this->priceProductScheduleStoreFinder
            ->findStoreByName($priceProductTransfer->getMoneyValue()->getStore()->getName());

        if ($storeTransfer !== null) {
            $priceProductTransfer->getMoneyValue()
                ->setFkStore($storeTransfer->getIdStore());
        }

        return $priceProductTransfer;
    }
}