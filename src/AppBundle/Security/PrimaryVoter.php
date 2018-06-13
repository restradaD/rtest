<?php

namespace AppBundle\Security;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class PrimaryVoter extends Voter
{
    const MANAGE = 'manage';
    const INDEX = 'index';
    const CREATE = 'new';
    const UPDATE = 'edit';
    const DELETE = 'delete';
    const SHOW = 'show';
    const ASSIGN = 'assign';

    private $decisionManager;
    private $entityManager;
    private $className;

	public function __construct(AccessDecisionManagerInterface $decisionManager, EntityManagerInterface $entityManager)
    {
        $this->decisionManager = $decisionManager;
        $this->entityManager = $entityManager;
    }

    protected function supports($attribute, $subject)
    {
        $protectedEntities = [
            'AppBundle\Entity\User',
            'AppBundle\Entity\Company',
            'AppBundle\Entity\Permission',
        ];

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::MANAGE, self::INDEX, self::CREATE, self::UPDATE, self::DELETE, self::SHOW, self::ASSIGN])) {
            return false;
        }

        if (is_object($subject)) {
            $this->className = ClassUtils::getRealClass(get_class($subject));
        } else {
            $this->className = $subject;
        }

        // only vote on protected entities inside this voter
        if (!in_array($this->className, $protectedEntities)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $currentUser = $token->getUser();

        if (!is_object($currentUser)) {
            // The user must be logged in; if not, deny access.
            return false;
        }

        $chunkName = explode('\\', $this->className);
        $entityName = strtolower(end($chunkName));

        $roles = $currentUser->getRoles();
        $rolePermissionsCount = $this->entityManager->getRepository('AppBundle:RolePermission')
            ->findByRoles($roles, $attribute, $entityName);

        return $rolePermissionsCount > 0 ? true : false;
    }
}
