<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Order;

use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType as BaseOrderItemType;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItemType extends BaseOrderItemType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('unitPrice');

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $data = $event->getData();
                if (isset($data['variant'])) {
                    $form = $event->getForm();
                    $form
                        ->add('variant', 'entity_hidden', [
                            'data_class' => $options['variant_data_class'],
                        ])
                        ->add('unitPrice', IntegerType::class, [
                            'data' => $data['variant']->getPrice()
                        ])
                    ;
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'variant_data_class' => ProductVariant::class,
        ]);
    }
}
