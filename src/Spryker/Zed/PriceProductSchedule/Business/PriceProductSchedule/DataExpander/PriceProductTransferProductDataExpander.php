<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander;

use Generated\Shared\Transfer\PriceProductTransfer;
use Spryker\Zed\PriceProductSchedule\Business\Product\ProductFinderInterface;

class PriceProductTransferProductDataExpander implements PriceProductTransferDataExpanderInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\Product\ProductFinderInterface
     */
    protected $productFinder;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Business\Product\ProductFinderInterface $productFinder
     */
    public function __construct(
        ProductFinderInterface $productFinder
    ) {
        $this->productFinder = $productFinder;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductTransfer $priceProductTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductTransfer
     */
    public function expand(PriceProductTransfer $priceProductTransfer): PriceProductTransfer
    {
        if ($priceProductTransfer->getSkuProductAbstract()) {
            $productAbstractId = $this->productFinder
                ->findProductAbstractIdBySku($priceProductTransfer->getSkuProductAbstract());

            if ($productAbstractId !== null) {
                $priceProductTransfer->setIdProductAbstract($productAbstractId);
            }
        }

        if ($priceProductTransfer->getSkuProduct()) {
            $productConcreteId = $this->productFinder
                ->findProductConcreteIdBySku($priceProductTransfer->getSkuProduct());

            if ($productConcreteId !== null) {
                $priceProductTransfer->setIdProduct($productConcreteId);
            }
        }

        return $priceProductTransfer;
    }
}