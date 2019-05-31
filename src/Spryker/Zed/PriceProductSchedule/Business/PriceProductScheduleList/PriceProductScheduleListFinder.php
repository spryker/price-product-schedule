<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList;

use Generated\Shared\Transfer\PriceProductScheduleListErrorTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListRequestTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListResponseTransfer;
use Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface;

class PriceProductScheduleListFinder implements PriceProductScheduleListFinderInterface
{
    protected const ERROR_MESSAGE_PRICE_PRODUCT_SCHEDULE_LIST_NOT_FOUND = 'Price product schedule list was not found by provided id %s';

    /**
     * @var \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface
     */
    protected $priceProductScheduleRepository;

    /**
     * @param \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface $priceProductScheduleRepository
     */
    public function __construct(
        PriceProductScheduleRepositoryInterface $priceProductScheduleRepository
    ) {
        $this->priceProductScheduleRepository = $priceProductScheduleRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\PriceProductScheduleListRequestTransfer $priceProductScheduleListRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductScheduleListResponseTransfer
     */
    public function findPriceProductScheduleList(
        PriceProductScheduleListRequestTransfer $priceProductScheduleListRequestTransfer
    ): PriceProductScheduleListResponseTransfer {
        $priceProductScheduleListResponseTransfer = (new PriceProductScheduleListResponseTransfer())
            ->setIsSuccess(false);

        $priceProductScheduleListTransfer = $this->priceProductScheduleRepository->findPriceProductScheduleListById($priceProductScheduleListRequestTransfer);

        if ($priceProductScheduleListTransfer === null) {
            $error = (new PriceProductScheduleListErrorTransfer())
                ->setMessage(
                    sprintf(
                        static::ERROR_MESSAGE_PRICE_PRODUCT_SCHEDULE_LIST_NOT_FOUND,
                        $priceProductScheduleListRequestTransfer->getIdPriceProductScheduleList()
                    )
                );

            return $priceProductScheduleListResponseTransfer->addError($error);
        }

        return $priceProductScheduleListResponseTransfer
            ->setIsSuccess(true)
            ->setPriceProductScheduleList($priceProductScheduleListTransfer);
    }
}