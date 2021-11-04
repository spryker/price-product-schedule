<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList;

use Generated\Shared\Transfer\PriceProductScheduleListResponseTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListTransfer;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Expander\PriceProductScheduleListUserExpanderInterface;
use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig;

class PriceProductScheduleListCreator implements PriceProductScheduleListCreatorInterface
{
    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface
     */
    protected $priceProductScheduleEntityManager;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Expander\PriceProductScheduleListUserExpanderInterface
     */
    protected $priceProductScheduleListUserExpander;

    /**
     * @var \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig
     */
    protected $priceProductScheduleConfig;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface $priceProductScheduleEntityManager
     * @param \Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Expander\PriceProductScheduleListUserExpanderInterface $priceProductScheduleListUserExpander
     * @param \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig $priceProductScheduleConfig
     */
    public function __construct(
        PriceProductScheduleEntityManagerInterface $priceProductScheduleEntityManager,
        PriceProductScheduleListUserExpanderInterface $priceProductScheduleListUserExpander,
        PriceProductScheduleConfig $priceProductScheduleConfig
    ) {
        $this->priceProductScheduleEntityManager = $priceProductScheduleEntityManager;
        $this->priceProductScheduleListUserExpander = $priceProductScheduleListUserExpander;
        $this->priceProductScheduleConfig = $priceProductScheduleConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleListTransfer $priceProductScheduleListTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductScheduleListResponseTransfer
     */
    public function createPriceProductScheduleList(
        PriceProductScheduleListTransfer $priceProductScheduleListTransfer
    ): PriceProductScheduleListResponseTransfer {
        $priceProductScheduleListTransfer = $this->priceProductScheduleListUserExpander
            ->expand($priceProductScheduleListTransfer);

        $priceProductScheduleListTransfer = $this->priceProductScheduleEntityManager
            ->createPriceProductScheduleList($priceProductScheduleListTransfer);

        return (new PriceProductScheduleListResponseTransfer())
            ->setIsSuccess(true)
            ->setPriceProductScheduleList($priceProductScheduleListTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\PriceProductScheduleListTransfer
     */
    public function createDefaultPriceProductScheduleList(): PriceProductScheduleListTransfer
    {
        $priceProductScheduleListTransfer = new PriceProductScheduleListTransfer();
        $priceProductScheduleListTransfer->setName(
            $this->priceProductScheduleConfig->getPriceProductScheduleListDefaultName(),
        );
        $priceProductScheduleListTransfer->setIsActive(true);

        return $this->createPriceProductScheduleList($priceProductScheduleListTransfer)
            ->getPriceProductScheduleList();
    }
}
