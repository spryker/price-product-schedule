<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProduct;

use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig;

class PriceProductFallbackFinder implements PriceProductFallbackFinderInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig
     */
    protected $priceProductScheduleConfig;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface
     */
    protected $priceProductFacade;

    public function __construct(
        PriceProductScheduleConfig $priceProductScheduleConfig,
        PriceProductScheduleToPriceProductFacadeInterface $priceProductFacade
    ) {
        $this->priceProductScheduleConfig = $priceProductScheduleConfig;
        $this->priceProductFacade = $priceProductFacade;
    }

    public function findFallbackPriceProduct(PriceProductTransfer $priceProductTransfer, ?StoreTransfer $storeTransfer): ?PriceProductTransfer
    {
        $priceProductTransfer->requireMoneyValue();
        $fallbackPriceTypeName = $this->findFallbackPriceType($priceProductTransfer->getPriceTypeName());

        if ($fallbackPriceTypeName === null) {
            return null;
        }

        $priceProductFilterTransfer = (new PriceProductFilterTransfer())
            ->setPriceTypeName($fallbackPriceTypeName)
            ->setCurrencyIsoCode($priceProductTransfer->getMoneyValue()->getCurrency()->getCode());

        if ($storeTransfer) {
            $priceProductFilterTransfer->setStoreName($storeTransfer->getNameOrFail());
        }

        if ($priceProductTransfer->getSkuProductAbstract() !== null) {
            $priceProductFilterTransfer->setSku($priceProductTransfer->getSkuProductAbstract());
        }

        if ($priceProductTransfer->getSkuProduct() !== null) {
            $priceProductFilterTransfer->setSku($priceProductTransfer->getSkuProduct());
        }

        return $this->priceProductFacade->findPriceProductFor($priceProductFilterTransfer);
    }

    protected function findFallbackPriceType(string $priceTypeName): ?string
    {
        $fallBackPriceTypeList = $this->priceProductScheduleConfig->getFallbackPriceTypeList();

        return $fallBackPriceTypeList[$priceTypeName] ?? null;
    }
}
