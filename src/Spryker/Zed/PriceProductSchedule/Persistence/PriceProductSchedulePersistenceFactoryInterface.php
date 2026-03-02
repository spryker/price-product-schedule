<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence;

use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleQuery;

interface PriceProductSchedulePersistenceFactoryInterface
{
    public function createPriceProductScheduleQuery(): SpyPriceProductScheduleQuery;
}
