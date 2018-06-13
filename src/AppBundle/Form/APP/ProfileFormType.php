<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\APP;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProfileFormType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildUserForm($builder, $options);

		$constraintsOptions = array(
			'message' => 'fos_user.current_password.invalid',
		);

		if (!empty($options['validation_groups'])) {
			$constraintsOptions['groups'] = array(reset($options['validation_groups']));
		}

		$builder->add('current_password', PasswordType::class, array(
			'label' => 'app.current_password',
			'translation_domain' => 'AppBundle',
			'mapped' => false,
			'constraints' => new UserPassword($constraintsOptions),
		));
	}

	public function getParent()
	{
		return 'FOS\UserBundle\Form\Type\ProfileFormType';
	}

	// BC for SF < 3.0
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->getBlockPrefix();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'app_user_profile';
	}

	/**
	 * Builds the embedded form representing the user.
	 *
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	protected function buildUserForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', null, array('label' => 'app.username', 'translation_domain' => 'AppBundle'))
			->add('email', EmailType::class, array('label' => 'app.email', 'translation_domain' => 'AppBundle'))
			->add('first_name', null, ['label' => 'app.first_name', 'translation_domain' => 'AppBundle'])
			->add('last_name', null, ['label' => 'app.last_name', 'translation_domain' => 'AppBundle'])
			->add('photo', VichFileType::class, ['download_uri' => true, 'label' => 'app.profile_picture', 'translation_domain' => 'AppBundle'])
			->add('apikey', null, ['label' => 'app.apikey', 'translation_domain' => 'AppBundle', 'disabled' => true])
		;
	}
}
