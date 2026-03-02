<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceType;

use Generated\Shared\Transfer\PriceTypeTransfer;

interface PriceTypeFinderInterface
{
    public function findPriceTypeByName(string $priceTypeName): ?PriceTypeTransfer;
}
