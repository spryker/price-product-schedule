<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\Product;

interface ProductFinderInterface
{
    public function findProductAbstractIdBySku(string $sku): ?int;

    public function findProductConcreteIdBySku(string $sku): ?int;
}
