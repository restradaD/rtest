<?php

namespace AppBundle\Form\API;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'app.title', 'translation_domain' => 'AppBundle'])
            ->add('description', null, ['label' => 'app.description', 'translation_domain' => 'AppBundle'])
            ->add('notificationType', null, ['label' => 'app.type', 'translation_domain' => 'AppBundle'])
            ->add('notificationChannels', null, ['label' => 'app.channels', 'translation_domain' => 'AppBundle'])
            ->add('to', null, ['label' => 'app.users.to', 'translation_domain' => 'AppBundle'])
            ->add('path', null, ['label' => 'app.notifications.path', 'translation_domain' => 'AppBundle'])
            ->add('parameters', null, ['label' => 'app.notifications.parameters', 'translation_domain' => 'AppBundle'])
            ->add('url', null, ['label' => 'app.notifications.url', 'translation_domain' => 'AppBundle'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Notification'
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
