<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\StoreAwareConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleFacadeInterface getFacade()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface getRepository()
 */
class PriceProductScheduleApplyConsole extends StoreAwareConsole
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'price-product-schedule:apply';

    /**
     * @var string
     */
    public const DESCRIPTION = 'Apply scheduled prices that meet the requirements';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getFacade()->applyScheduledPrices($this->getStore($input));

        return static::CODE_SUCCESS;
    }
}
