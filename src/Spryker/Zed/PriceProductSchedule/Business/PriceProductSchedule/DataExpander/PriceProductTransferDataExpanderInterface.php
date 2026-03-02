<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander;

use Generated\Shared\Transfer\PriceProductTransfer;

interface PriceProductTransferDataExpanderInterface
{
    public function expand(
        PriceProductTransfer $priceProductTransfer
    ): PriceProductTransfer;
}
