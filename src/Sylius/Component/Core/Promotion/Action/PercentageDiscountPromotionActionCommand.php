<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PercentageDiscountPromotionActionCommand extends DiscountPromotionActionCommand
{
    const TYPE = 'order_percentage_discount';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var UnitsPromotionAdjustmentsApplicatorInterface
     */
    private $unitsPromotionAdjustmentsApplicator;

    /**
     * @param ProportionalIntegerDistributorInterface $distributor
     * @param UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
     */
    public function __construct(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->distributor = $distributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        /** @var OrderInterface $subject */
        if (!$this->isSubjectValid($subject)) {
            return;
        }

        $this->isConfigurationValid($configuration);

        $promotionAmount = $this->calculateAdjustmentAmount($subject->getPromotionSubjectTotal(), $configuration['percentage']);
        if (0 === $promotionAmount) {
            return;
        }

        $itemsTotal = [];
        foreach ($subject->getItems() as $orderItem) {
            $itemsTotal[] = $orderItem->getTotal();
        }

        $splitPromotion = $this->distributor->distribute($itemsTotal, $promotionAmount);
        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_percentage_discount_configuration';
    }

    /**
     * {@inheritdoc}
     */
    protected function isConfigurationValid(array $configuration)
    {
        if (!isset($configuration['percentage']) || !is_float($configuration['percentage'])) {
            throw new \InvalidArgumentException('"percentage" must be set and must be a float.');
        }
    }

    /**
     * @param int $promotionSubjectTotal
     * @param int $percentage
     *
     * @return int
     */
    private function calculateAdjustmentAmount($promotionSubjectTotal, $percentage)
    {
        return -1 * (int) round($promotionSubjectTotal * $percentage);
    }
}
