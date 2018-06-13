<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Notification;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CheckOwnershipVoter
 * @package AppBundle\Security
 */
class CheckOwnershipVoter extends Voter
{
    /**
     * Verb
     */
    const OWNER = 'owner';

    /**
     * User class
     */
    const USER = 'AppBundle\Entity\User';

    /**
     * Notification class
     */
    const NOTIFICATION = 'AppBundle\Entity\Notification';

    /**
     * Doctrine entity manager
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string $className
     */
    protected $className;

    /** {@inheritdoc} */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** {@inheritdoc} */
    protected function supports($attribute, $subject)
    {
        $protectedEntities = [
            self::NOTIFICATION,
            self::USER,
        ];

        if (!in_array($attribute, [self::OWNER])) {
            return false;
        }

        $this->className = ClassUtils::getRealClass(get_class($subject));

        if (!in_array($this->className, $protectedEntities)) {
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->entityManager;

        /** @var \Symfony\Component\Security\Core\User\User $sysUser */
        $sysUser = $token->getUser();

        /** @var User $user */
        $user = $entityManager->getRepository('AppBundle:User')
            ->findOneValidBy(['username' => $sysUser->getUsername()]);

        /** @var Company $company */
        $company = $entityManager->getRepository('AppBundle:Company')
            ->findOneByUsername($user->getUsername());


        switch ($this->className) {
            case self::USER:
                return $this->checkUser($subject, $company);
            case self::NOTIFICATION:
                return $this->checkNotification($subject, $company);
        }

        throw new \LogicException('Invalid Option.');
    }

    /**
     * Check if notification entity is available for $user
     * @param Notification $notification
     * @param Company $company
     * @return bool
     */
    public function checkNotification(Notification $notification, Company $company)
    {
        return ($notification->getTo()->getCompany() == $company) ? true : false;
    }

    /**
     * Check if user entity is available for $user.
     * @param User $user
     * @param Company $company
     * @return bool
     * */
    protected function checkUser(User $user, Company $company)
    {
        return ($user->getCompany() === $company) ? true : false;
    }
}