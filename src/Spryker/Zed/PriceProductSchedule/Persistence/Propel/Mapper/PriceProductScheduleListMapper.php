<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PriceProductScheduleListMetaDataTransfer;
use Generated\Shared\Transfer\PriceProductScheduleListTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\PriceProductSchedule\Persistence\SpyPriceProductScheduleList;
use Spryker\Zed\PriceProductSchedule\Persistence\Finder\PriceProductScheduleListFinder;

class PriceProductScheduleListMapper implements PriceProductScheduleListMapperInterface
{
    public function mapPriceProductScheduleListEntityToPriceProductScheduleListTransfer(
        SpyPriceProductScheduleList $priceProductScheduleListEntity,
        PriceProductScheduleListTransfer $priceProductScheduleListTransfer
    ): PriceProductScheduleListTransfer {
        $priceProductScheduleListTransfer
            ->fromArray($priceProductScheduleListEntity->toArray(), true);

        $priceProductScheduleListMetadataTransfer = $this->mapPriceProductScheduleListEntityToPriceProductScheduleListMetaDataTransfer(
            $priceProductScheduleListEntity,
            new PriceProductScheduleListMetaDataTransfer(),
        );
        $userTransfer = $this->mapPriceProductScheduleListEntityToUserTransfer($priceProductScheduleListEntity, new UserTransfer());

        return $priceProductScheduleListTransfer->setMetaData($priceProductScheduleListMetadataTransfer)
            ->setUser($userTransfer);
    }

    public function mapPriceProductScheduleListTransferToPriceProductScheduleListEntity(
        PriceProductScheduleListTransfer $priceProductScheduleListTransfer,
        SpyPriceProductScheduleList $priceProductScheduleListEntity
    ): SpyPriceProductScheduleList {
        $priceProductScheduleListEntity
            ->fromArray($priceProductScheduleListTransfer->toArray());

        return $priceProductScheduleListEntity;
    }

    protected function mapPriceProductScheduleListEntityToUserTransfer(
        SpyPriceProductScheduleList $priceProductScheduleListEntity,
        UserTransfer $userTransfer
    ): ?UserTransfer {
        $userEntity = $priceProductScheduleListEntity->getUser();

        if ($userEntity === null) {
            return null;
        }

        return $userTransfer->fromArray($userEntity->toArray(), true);
    }

    protected function mapPriceProductScheduleListEntityToPriceProductScheduleListMetaDataTransfer(
        SpyPriceProductScheduleList $priceProductScheduleListEntity,
        PriceProductScheduleListMetaDataTransfer $priceProductScheduleListMetadataTransfer
    ): PriceProductScheduleListMetaDataTransfer {
        if ($priceProductScheduleListEntity->hasVirtualColumn(PriceProductScheduleListFinder::ALIAS_NUMBER_OF_PRICES)) {
            $priceProductScheduleListMetadataTransfer->setNumberOfPrices(
                $priceProductScheduleListEntity->getVirtualColumn(PriceProductScheduleListFinder::ALIAS_NUMBER_OF_PRICES),
            );
        }

        if ($priceProductScheduleListEntity->hasVirtualColumn(PriceProductScheduleListFinder::ALIAS_NUMBER_OF_PRODUCTS)) {
            $priceProductScheduleListMetadataTransfer->setNumberOfProducts(
                $priceProductScheduleListEntity->getVirtualColumn(PriceProductScheduleListFinder::ALIAS_NUMBER_OF_PRODUCTS),
            );
        }

        return $priceProductScheduleListMetadataTransfer;
    }
}
