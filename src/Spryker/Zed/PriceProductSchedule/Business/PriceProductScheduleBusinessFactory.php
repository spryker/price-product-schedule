<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\PriceProductSchedule\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\PriceProductSchedule\Business\Currency\CurrencyFinder;
use Spryker\Zed\PriceProductSchedule\Business\Currency\CurrencyFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProduct\PriceProductFallbackFinder;
use Spryker\Zed\PriceProductSchedule\Business\PriceProduct\PriceProductFallbackFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProduct\PriceProductUpdater;
use Spryker\Zed\PriceProductSchedule\Business\PriceProduct\PriceProductUpdaterInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Applier\AbstractProductPriceProductScheduleApplier;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Applier\AbstractProductPriceProductScheduleApplierInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Applier\ConcreteProductPriceProductScheduleApplier;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Applier\ConcreteProductPriceProductScheduleApplierInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Creator\PriceProductScheduleCreator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Creator\PriceProductScheduleCreatorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferDataExpanderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferMoneyValueDataExpander;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferPriceDimensionDataExpander;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferPriceTypeDataExpander;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferProductDataExpander;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Executor\PriceProductScheduleApplyTransactionExecutor;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Executor\PriceProductScheduleApplyTransactionExecutorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\CurrencyDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\DateDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\ImportDataValidatorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\PriceDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\PriceTypeDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\ProductDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\StoreDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\UniqueDataValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleApplier;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleApplierInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCleaner;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCleanerInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCsvReader;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCsvReaderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCsvValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleCsvValidatorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleDisabler;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleDisablerInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleImportMapper;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleImportMapperInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleImportValidator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleImportValidatorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleMapper;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\PriceProductScheduleMapperInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Remover\PriceProductScheduleRemover;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Remover\PriceProductScheduleRemoverInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Resolver\PriceProductScheduleApplierByProductTypeResolver;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Resolver\PriceProductScheduleApplierByProductTypeResolverInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Updater\PriceProductScheduleUpdater;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\Updater\PriceProductScheduleUpdaterInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Expander\PriceProductScheduleListUserExpander;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Expander\PriceProductScheduleListUserExpanderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListCreator;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListCreatorInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListFinder;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListImporter;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListImporterInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListUpdater;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\PriceProductScheduleListUpdaterInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Remover\PriceProductScheduleListRemover;
use Spryker\Zed\PriceProductSchedule\Business\PriceProductScheduleList\Remover\PriceProductScheduleListRemoverInterface;
use Spryker\Zed\PriceProductSchedule\Business\PriceType\PriceTypeFinder;
use Spryker\Zed\PriceProductSchedule\Business\PriceType\PriceTypeFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\Product\ProductFinder;
use Spryker\Zed\PriceProductSchedule\Business\Product\ProductFinderInterface;
use Spryker\Zed\PriceProductSchedule\Business\Store\StoreFinder;
use Spryker\Zed\PriceProductSchedule\Business\Store\StoreFinderInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToCurrencyFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToPriceProductFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToProductFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToStoreFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Facade\PriceProductScheduleToUserFacadeInterface;
use Spryker\Zed\PriceProductSchedule\Dependency\Service\PriceProductScheduleToUtilCsvServiceInterface;
use Spryker\Zed\PriceProductSchedule\PriceProductScheduleDependencyProvider;

/**
 * @method \Spryker\Zed\PriceProductSchedule\PriceProductScheduleConfig getConfig()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\PriceProductSchedule\Persistence\PriceProductScheduleRepositoryInterface getRepository()
 */
class PriceProductScheduleBusinessFactory extends AbstractBusinessFactory
{
    public function createPriceProductScheduleCleaner(): PriceProductScheduleCleanerInterface
    {
        return new PriceProductScheduleCleaner(
            $this->getEntityManager(),
        );
    }

    public function createPriceProductScheduleApplier(): PriceProductScheduleApplierInterface
    {
        return new PriceProductScheduleApplier(
            $this->createPriceProductScheduleDisabler(),
            $this->getRepository(),
            $this->getStoreFacade(),
            $this->createPriceProductScheduleApplyTransactionExecutor(),
        );
    }

    public function createAbstractProductPriceProductScheduleApplier(): AbstractProductPriceProductScheduleApplierInterface
    {
        return new AbstractProductPriceProductScheduleApplier(
            $this->getStoreFacade(),
            $this->getRepository(),
            $this->createPriceProductScheduleApplyTransactionExecutor(),
            $this->createPriceProductScheduleDisabler(),
        );
    }

    public function createConcreteProductPriceProductScheduleApplier(): ConcreteProductPriceProductScheduleApplierInterface
    {
        return new ConcreteProductPriceProductScheduleApplier(
            $this->getStoreFacade(),
            $this->getRepository(),
            $this->createPriceProductScheduleApplyTransactionExecutor(),
            $this->createPriceProductScheduleDisabler(),
        );
    }

    public function createPriceProductScheduleApplierByProductTypeResolver(): PriceProductScheduleApplierByProductTypeResolverInterface
    {
        return new PriceProductScheduleApplierByProductTypeResolver(
            $this->createAbstractProductPriceProductScheduleApplier(),
            $this->createConcreteProductPriceProductScheduleApplier(),
        );
    }

    public function createPriceProductScheduleApplyTransactionExecutor(): PriceProductScheduleApplyTransactionExecutorInterface
    {
        return new PriceProductScheduleApplyTransactionExecutor(
            $this->createPriceProductScheduleDisabler(),
            $this->getPriceProductFacade(),
            $this->getEntityManager(),
        );
    }

    public function createPriceProductScheduleDisabler(): PriceProductScheduleDisablerInterface
    {
        return new PriceProductScheduleDisabler(
            $this->getEntityManager(),
            $this->getRepository(),
            $this->createPriceProductFallbackFinder(),
            $this->createProductPriceUpdater(),
            $this->getPriceProductFacade(),
        );
    }

    public function createPriceProductFallbackFinder(): PriceProductFallbackFinderInterface
    {
        return new PriceProductFallbackFinder(
            $this->getConfig(),
            $this->getPriceProductFacade(),
        );
    }

    public function createProductPriceUpdater(): PriceProductUpdaterInterface
    {
        return new PriceProductUpdater(
            $this->getEntityManager(),
            $this->getPriceProductFacade(),
        );
    }

    public function createPriceProductScheduleListCreator(): PriceProductScheduleListCreatorInterface
    {
        return new PriceProductScheduleListCreator(
            $this->getEntityManager(),
            $this->createPriceProductScheduleListExpander(),
            $this->getConfig(),
        );
    }

    public function createPriceProductScheduleListExpander(): PriceProductScheduleListUserExpanderInterface
    {
        return new PriceProductScheduleListUserExpander(
            $this->getUserFacade(),
        );
    }

    public function createPriceProductScheduleListUpdater(): PriceProductScheduleListUpdaterInterface
    {
        return new PriceProductScheduleListUpdater(
            $this->getEntityManager(),
        );
    }

    public function createPriceProductScheduleListImporter(): PriceProductScheduleListImporterInterface
    {
        return new PriceProductScheduleListImporter(
            $this->getEntityManager(),
            $this->createPriceProductScheduleImportValidator(),
            $this->createPriceProductScheduleMapper(),
            $this->getPriceProductTransferDataExpanderList(),
        );
    }

    public function createPriceProductScheduleImportValidator(): PriceProductScheduleImportValidatorInterface
    {
        return new PriceProductScheduleImportValidator($this->createPriceProductScheduleImportDataValidatorList());
    }

    public function createPriceProductScheduleMapper(): PriceProductScheduleMapperInterface
    {
        return new PriceProductScheduleMapper();
    }

    public function createPriceProductScheduleImportMapper(): PriceProductScheduleImportMapperInterface
    {
        return new PriceProductScheduleImportMapper($this->getConfig());
    }

    /**
     * @return array<\Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\DataExpander\PriceProductTransferDataExpanderInterface>
     */
    public function getPriceProductTransferDataExpanderList(): array
    {
        return [
            $this->createPriceProductTransferPriceDimensionDataExpander(),
            $this->createPriceProductTransferMoneyValueDataExpander(),
            $this->createPriceProductTransferPriceTypeDataExpander(),
            $this->createPriceProductTransferProductDataExpander(),
        ];
    }

    public function createPriceProductTransferPriceDimensionDataExpander(): PriceProductTransferDataExpanderInterface
    {
        return new PriceProductTransferPriceDimensionDataExpander($this->getConfig());
    }

    public function createPriceProductTransferMoneyValueDataExpander(): PriceProductTransferDataExpanderInterface
    {
        return new PriceProductTransferMoneyValueDataExpander(
            $this->createStoreFinder(),
            $this->createCurrencyFinder(),
        );
    }

    public function createPriceProductTransferPriceTypeDataExpander(): PriceProductTransferDataExpanderInterface
    {
        return new PriceProductTransferPriceTypeDataExpander($this->createPriceTypeFinder());
    }

    public function createPriceProductTransferProductDataExpander(): PriceProductTransferDataExpanderInterface
    {
        return new PriceProductTransferProductDataExpander($this->createProductFinder());
    }

    public function createCurrencyFinder(): CurrencyFinderInterface
    {
        return new CurrencyFinder($this->getCurrencyFacade());
    }

    public function createStoreFinder(): StoreFinderInterface
    {
        return new StoreFinder($this->getStoreFacade());
    }

    public function createPriceTypeFinder(): PriceTypeFinderInterface
    {
        return new PriceTypeFinder($this->getPriceProductFacade());
    }

    public function createProductFinder(): ProductFinderInterface
    {
        return new ProductFinder($this->getProductFacade());
    }

    public function createPriceProductScheduleListFinder(): PriceProductScheduleListFinderInterface
    {
        return new PriceProductScheduleListFinder(
            $this->getRepository(),
            $this->getConfig(),
        );
    }

    public function createCurrencyDataValidator(): ImportDataValidatorInterface
    {
        return new CurrencyDataValidator($this->createCurrencyFinder());
    }

    public function createDateDataValidator(): ImportDataValidatorInterface
    {
        return new DateDataValidator();
    }

    public function createPriceDataValidator(): ImportDataValidatorInterface
    {
        return new PriceDataValidator();
    }

    public function createPriceTypeDataValidator(): ImportDataValidatorInterface
    {
        return new PriceTypeDataValidator($this->createPriceTypeFinder());
    }

    public function createProductDataValidator(): ImportDataValidatorInterface
    {
        return new ProductDataValidator($this->createProductFinder());
    }

    public function createStoreDataValidator(): ImportDataValidatorInterface
    {
        return new StoreDataValidator($this->createStoreFinder());
    }

    public function createUniqueDataValidator(): ImportDataValidatorInterface
    {
        return new UniqueDataValidator(
            $this->getRepository(),
            $this->createPriceProductScheduleImportMapper(),
        );
    }

    /**
     * @return array<\Spryker\Zed\PriceProductSchedule\Business\PriceProductSchedule\ImportDataValidator\ImportDataValidatorInterface>
     */
    public function createPriceProductScheduleImportDataValidatorList(): array
    {
        return [
            $this->createCurrencyDataValidator(),
            $this->createDateDataValidator(),
            $this->createPriceDataValidator(),
            $this->createPriceTypeDataValidator(),
            $this->createProductDataValidator(),
            $this->createStoreDataValidator(),
            $this->createUniqueDataValidator(),
        ];
    }

    public function createPriceProductScheduleCsvReader(): PriceProductScheduleCsvReaderInterface
    {
        return new PriceProductScheduleCsvReader(
            $this->getUtilCsvService(),
            $this->createPriceProductScheduleImportMapper(),
        );
    }

    public function createPriceProductScheduleCsvValidator(): PriceProductScheduleCsvValidatorInterface
    {
        return new PriceProductScheduleCsvValidator(
            $this->getUtilCsvService(),
            $this->getConfig(),
        );
    }

    public function getUserFacade(): PriceProductScheduleToUserFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_USER);
    }

    public function createPriceProductScheduleRemover(): PriceProductScheduleRemoverInterface
    {
        return new PriceProductScheduleRemover(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createPriceProductScheduleApplierByProductTypeResolver(),
            $this->createPriceProductScheduleDisabler(),
        );
    }

    public function createPriceProductScheduleUpdater(): PriceProductScheduleUpdaterInterface
    {
        return new PriceProductScheduleUpdater(
            $this->getEntityManager(),
            $this->createPriceProductScheduleApplierByProductTypeResolver(),
        );
    }

    public function createPriceProductScheduleCreator(): PriceProductScheduleCreatorInterface
    {
        return new PriceProductScheduleCreator(
            $this->getEntityManager(),
            $this->createPriceProductScheduleApplierByProductTypeResolver(),
            $this->createPriceProductScheduleListFinder(),
            $this->createPriceProductScheduleListCreator(),
        );
    }

    public function createPriceProductScheduleListRemover(): PriceProductScheduleListRemoverInterface
    {
        return new PriceProductScheduleListRemover(
            $this->createPriceProductScheduleRemover(),
            $this->createPriceProductScheduleListFinder(),
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    public function getPriceProductFacade(): PriceProductScheduleToPriceProductFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    public function getStoreFacade(): PriceProductScheduleToStoreFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_STORE);
    }

    public function getProductFacade(): PriceProductScheduleToProductFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_PRODUCT);
    }

    public function getCurrencyFacade(): PriceProductScheduleToCurrencyFacadeInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::FACADE_CURRENCY);
    }

    public function getUtilCsvService(): PriceProductScheduleToUtilCsvServiceInterface
    {
        return $this->getProvidedDependency(PriceProductScheduleDependencyProvider::SERVICE_UTIL_CSV);
    }
}
