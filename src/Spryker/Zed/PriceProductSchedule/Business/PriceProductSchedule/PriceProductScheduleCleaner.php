<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule;

use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface;

class PriceProductScheduleCleaner implements PriceProductScheduleCleanerInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface
     */
    protected $priceProductScheduleEntityManager;

    public function __construct(PriceProductScheduleEntityManagerInterface $priceProductScheduleEntityManager)
    {
        $this->priceProductScheduleEntityManager = $priceProductScheduleEntityManager;
    }

    public function cleanAppliedScheduledPrices(int $daysRetained): void
    {
        $this->priceProductScheduleEntityManager->deleteOldScheduledPrices($daysRetained);
    }
}
