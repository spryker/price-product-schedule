<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

class PriceProductScheduleToStoreFacadeBridge implements PriceProductScheduleToStoreFacadeInterface
{
    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     */
    public function __construct($storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    public function getCurrentStore(): StoreTransfer
    {
        return $this->storeFacade->getCurrentStore();
    }

    public function findStoreByName(string $storeName): ?StoreTransfer
    {
        return $this->storeFacade->findStoreByName($storeName);
    }

    /**
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array
    {
        return $this->storeFacade->getAllStores();
    }

    public function isCurrentStoreDefined(): bool
    {
        return $this->storeFacade->isCurrentStoreDefined();
    }
}
