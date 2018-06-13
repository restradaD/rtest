<?php

namespace AppBundle\Form\API;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', null, ['label' => 'app.code', 'translation_domain' => 'AppBundle'])
            ->add('description', null, ['label' => 'app.description', 'translation_domain' => 'AppBundle'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Permission'
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
