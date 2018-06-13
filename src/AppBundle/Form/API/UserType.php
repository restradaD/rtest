<?php

namespace AppBundle\Form\API;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['data'];
        $roles = $user->getRoles();

        foreach ($roles as $index => $role) {
            if ($role === User::ROLE_USER) {
                unset($roles[$index]);
            }
        }

        $roles = array_values($roles);

        $selectableRoles = User::ROLES_DEFINITION;
        foreach (User::SUPER_ADMIN_EXCLUSIVES_ROLES as $exclusiveRole) {
            unset($selectableRoles[$exclusiveRole]);
        }
        $selectableRoles = array_flip($selectableRoles);

        /** @var User $current_user */
        $current_user = $options['current_user'];

        if ($current_user && $current_user->hasRole(User::ROLE_SUPER_ADMIN)) {
            $builder
                ->add('company', null, ['required' => true, 'label' => 'app.company', 'translation_domain' => 'AppBundle'])
            ;
        }

        $builder
            ->add('first_name', null, ['label' => 'app.first_name', 'translation_domain' => 'AppBundle'])
            ->add('last_name', null, ['label' => 'app.last_name', 'translation_domain' => 'AppBundle'])
            ->add('username', null, ['label' => 'app.username', 'translation_domain' => 'AppBundle'])
            ->add('passcode', PasswordType::class, ['label' => 'app.password', 'translation_domain' => 'AppBundle'])
            ->add('photo', VichFileType::class, ['download_uri' => true, 'label' => 'app.profile_picture', 'translation_domain' => 'AppBundle'])
            ->add('roles', ChoiceType::class, ['choices' => $selectableRoles, 'multiple' => true, 'data' => $roles, 'label' => 'app.roles', 'translation_domain' => 'AppBundle'])
            ->add('email', null, ['label' => 'app.email', 'translation_domain' => 'AppBundle'])
            ->add('enabled', null, ['label' => 'app.enabled', 'translation_domain' => 'AppBundle'])
            ->add('deletedAt', DateTimeType::class, ['format' => 'yyyy-MM-dd', 'widget' => 'single_text', 'attr' => ['class' => 'datepicker'], 'label' => 'app.deleted_at', 'translation_domain' => 'AppBundle'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'current_user' => null
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
