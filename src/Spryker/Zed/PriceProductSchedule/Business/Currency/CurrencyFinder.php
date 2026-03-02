<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\Currency;

use Generated\Shared\Transfer\CurrencyTransfer;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToCurrencyFacadeInterface;

class CurrencyFinder implements CurrencyFinderInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToCurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var array
     */
    protected $currencyCache = [];

    public function __construct(PriceProductScheduleToCurrencyFacadeInterface $currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    public function findCurrencyByIsoCode(string $isoCode): ?CurrencyTransfer
    {
        if (isset($this->currencyCache[$isoCode])) {
            return $this->currencyCache[$isoCode];
        }

        $currencyTransfer = $this->currencyFacade->findCurrencyByIsoCode($isoCode);

        if ($currencyTransfer !== null) {
            $this->currencyCache[$isoCode] = $currencyTransfer;
        }

        return $currencyTransfer;
    }
}
