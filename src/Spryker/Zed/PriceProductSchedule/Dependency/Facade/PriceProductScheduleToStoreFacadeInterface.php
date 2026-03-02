<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

interface PriceProductScheduleToStoreFacadeInterface
{
    public function getCurrentStore(): StoreTransfer;

    public function findStoreByName(string $storeName): ?StoreTransfer;

    /**
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array;

    public function isCurrentStoreDefined(): bool;
}
