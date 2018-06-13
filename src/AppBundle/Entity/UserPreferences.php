<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserPreferences
 *
 * @ORM\Entity()
 * @ORM\Table(name="user_preferences")
 */
class UserPreferences
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="receive_daily_mail", type="boolean", nullable=true)
     */
    private $receiveDailyMail;

    /**
     * @ORM\OneToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     mappedBy="preferences"
     * )
     */
    private $user;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\NotificationPreferences",
     *     mappedBy="userPreferences"
     * )
     */
    private $notificationPreferences;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set receiveDailyMail
     *
     * @param boolean $receiveDailyMail
     *
     * @return UserPreferences
     */
    public function setReceiveDailyMail($receiveDailyMail)
    {
        $this->receiveDailyMail = $receiveDailyMail;

        return $this;
    }

    /**
     * Get receiveDailyMail
     *
     * @return bool
     */
    public function getReceiveDailyMail()
    {
        return $this->receiveDailyMail;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return UserPreferences
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notificationPreferences = new ArrayCollection();

    }

    /**
     * Add notificationPreferences
     *
     * @param NotificationPreferences $notificationPreferences
     *
     * @return UserPreferences
     */
    public function addNotificationPreferences(NotificationPreferences $notificationPreferences)
    {
        $this->notificationPreferences[] = $notificationPreferences;

        return $this;
    }

    /**
     * Remove notificationPreferences
     *
     * @param NotificationPreferences $notificationPreferences
     */
    public function removeNotificationPreferences(NotificationPreferences $notificationPreferences)
    {
        $this->notificationPreferences->removeElement($notificationPreferences);
    }

    /**
     * Get notificationPreferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationPreferences()
    {
        return $this->notificationPreferences;
    }
}
