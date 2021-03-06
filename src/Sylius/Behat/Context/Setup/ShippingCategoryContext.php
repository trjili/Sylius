<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ShippingCategoryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $shippingCategoryFactory;

    /**
     * @var RepositoryInterface
     */
    private $shippingCategoryRepository;

    /**
     * @var ObjectManager
     */
    private $shippingCategoryManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $shippingCategoryFactory
     * @param RepositoryInterface $shippingCategoryRepository
     * @param ObjectManager $shippingCategoryManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $shippingCategoryFactory,
        RepositoryInterface $shippingCategoryRepository,
        ObjectManager $shippingCategoryManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shippingCategoryFactory = $shippingCategoryFactory;
        $this->shippingCategoryRepository = $shippingCategoryRepository;
        $this->shippingCategoryManager = $shippingCategoryManager;
    }

    /**
     * @Given the store has :firstShippingCategoryName shipping category
     * @Given the store has :firstShippingCategoryName and :secondShippingCategoryName shipping category
     */
    public function theStoreHasAndShippingCategory($firstShippingCategoryName, $secondShippingCategoryName = null)
    {
        $this->createShippingCategory($firstShippingCategoryName);
        (null === $secondShippingCategoryName)? : $this->createShippingCategory($secondShippingCategoryName);
    }

    /**
     * @param string $shippingCategoryName
     */
    private function createShippingCategory($shippingCategoryName)
    {
        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory =  $this->shippingCategoryFactory->createNew();
        $shippingCategory->setName($shippingCategoryName);
        $shippingCategory->setCode(StringInflector::nameToCode($shippingCategoryName));

        $this->shippingCategoryRepository->add($shippingCategory);
        $this->sharedStorage->set('shipping_category', $shippingCategory);
    }
}
