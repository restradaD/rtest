<?php

namespace AppBundle\Form\API;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uuid', null, ['label' => 'app.uuid', 'translation_domain' => 'AppBundle'])
            ->add('os', null, ['label' => 'app.os', 'translation_domain' => 'AppBundle'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Device'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
