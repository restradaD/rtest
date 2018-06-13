<?php

namespace AppBundle\Form\API;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', null, ['label' => 'app.company', 'translation_domain' => 'AppBundle'])
            ->add('username', null, ['label' => 'app.username', 'translation_domain' => 'AppBundle'])
            ->add('passcode', PasswordType::class, ['label' => 'app.password', 'translation_domain' => 'AppBundle'])
            ->add('first_name', null, ['label' => 'app.first_name', 'translation_domain' => 'AppBundle'])
            ->add('last_name', null, ['label' => 'app.last_name', 'translation_domain' => 'AppBundle'])
            ->add('email', null, ['label' => 'app.email', 'translation_domain' => 'AppBundle'])
            ->add('locale', null, ['label' => 'app.locale', 'translation_domain' => 'AppBundle'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'cascade_validation' => true
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
