<?php

namespace AppBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function getUsernameForApiKey($apiKey)
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(['apiKey' => $apiKey, 'enabled' => true]);

        if (!$user) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return $user->getUsername();
    }

    public function loadUserByUsername($username)
    {

        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username, 'enabled' => true]);

        if (!$user) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        return new User(
            $username,
            null,
            $user->getRoles()
        );
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}