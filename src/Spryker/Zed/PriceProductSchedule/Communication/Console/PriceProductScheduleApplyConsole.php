<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Communication\Console;

use Generated\Shared\Transfer\PriceProductScheduledApplyRequestTransfer;
use Spryker\Zed\Kernel\Communication\Console\StoreAwareConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleFacadeInterface getFacade()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface getRepository()
 * @method \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleBusinessFactory getBusinessFactory()
 */
class PriceProductScheduleApplyConsole extends StoreAwareConsole
{
    public const string COMMAND_NAME = 'price-product-schedule:apply';

    public const string DESCRIPTION = 'Apply scheduled prices that meet the requirements';

    public const string OPTION_PROCESS_ALL = 'process-all';

    public const string OPTION_BATCH_SIZE = 'batch-size';

    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::DESCRIPTION)
            ->addOption(
                static::OPTION_PROCESS_ALL,
                null,
                InputOption::VALUE_NONE,
                'Process all available scheduled prices using a memory-efficient generator with a limit.',
            )
            ->addOption(
                static::OPTION_BATCH_SIZE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of scheduled prices to process per batch. Defaults to the configured batch size.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $priceProductScheduledApplyRequestTransfer = (new PriceProductScheduledApplyRequestTransfer())
            ->setStoreName($this->getStore($input) ?: null)
            ->setProcessAll((bool)$input->getOption(static::OPTION_PROCESS_ALL))
            ->setBatchSize($this->resolveBatchSize($input));

        $priceProductScheduledApplyResponseTransfer = $this->getFacade()
            ->applyAllScheduledPrices($priceProductScheduledApplyRequestTransfer);

        if ($priceProductScheduledApplyResponseTransfer->getErrors()->count() === 0) {
            return static::CODE_SUCCESS;
        }

        foreach ($priceProductScheduledApplyResponseTransfer->getErrors() as $errorTransfer) {
            $output->writeln(sprintf('<error>%s</error>', $errorTransfer->getMessage()));
        }

        return static::CODE_ERROR;
    }

    protected function resolveBatchSize(InputInterface $input): int
    {
        $batchSize = $input->getOption(static::OPTION_BATCH_SIZE);

        if ($batchSize) {
            return (int)$batchSize;
        }

        return $this->getBusinessFactory()->getConfig()->getApplyBatchSize();
    }
}
