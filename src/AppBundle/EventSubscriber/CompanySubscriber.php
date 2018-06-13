<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Company;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CompanySubscriber
 * @package AppBundle\EventSubscriber
 */
class CompanySubscriber implements EventSubscriber
{
    /** @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->container = $serviceContainer;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Company) {
            $company = $entity;

            $this->container->get('app.setup')->settings($company);
        }
    }
}