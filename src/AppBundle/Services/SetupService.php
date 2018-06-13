<?php

namespace AppBundle\Services;

use AppBundle\Entity\Company;
use AppBundle\Entity\Settings;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SetupService
{
    /** @var ContainerInterface $container */
    private $container;
    /** @var EntityManager $entityManager */
    private $entityManager;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    /**
     * Configure setup for new company (If needed)
     * @param Company $company
     * @return bool
     * */
    public function settings($company)
    {
        /** @var Company $superAdminCompany */
        $superAdminCompany = $this->entityManager->getRepository('AppBundle:Company')
            ->findOneBy([], ['createdAt' => 'ASC']);

        /** @var Settings $settings */
        $settings = $superAdminCompany->getSettings()[0];

        if ($settings && null == $company->getSettings()[0]) {
            /** @var EntityManager $entityManager */
            $entityManager = $this->entityManager;
            /** @var Settings $userCompanySettings */
            $userCompanySettings = new Settings();
            $userCompanySettings->setEmail($company->getEmail());
            $userCompanySettings->setUrl($settings->getUrl());
            $userCompanySettings->setApiUrl($settings->getApiUrl());
            $userCompanySettings->setCompany($company);

            $entityManager->persist($userCompanySettings);
            $entityManager->flush();

            return true;
        }

        return false;
    }
}