<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $zoneRepository;

    /**
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    /* @var ZoneInterface[] $zones */
                    $zones = $this->zoneRepository->findAll();
                    $zonesCodes = [];

                    foreach ($zones as $zone) {
                        $zonesCodes[$zone->getName()] = $zone->getCode();
                    }

                    return $zonesCodes;
                },
                'choice_translation_domain' => false,
                'label' => 'sylius.form.zone.types.zone',
                'placeholder' => 'sylius.form.zone.select',
                'choices_as_values' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_zone_code_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_zone_code_choice';
    }

    /**
     * @return array
     */
    private function getZoneCodes()
    {
        $zoneObjects = $this->zoneRepository->findAll();
        $zones = [];

        /* @var ZoneInterface $zone */
        foreach ($zoneObjects as $zone) {
            $zones[$zone->getCode()] = $zone->getName();
        }

        return $zones;
    }
}
