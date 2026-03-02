<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\Store;

use Generated\Shared\Transfer\StoreTransfer;

interface StoreFinderInterface
{
    public function findStoreByName(string $storeName): ?StoreTransfer;
}
