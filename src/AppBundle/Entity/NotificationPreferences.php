<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationPreferences
 *
 * @ORM\Entity()
 * @ORM\Table(name="notification_preferences")
 */
class NotificationPreferences
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
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\NotificationChannel",
     *     inversedBy="notificationPreferences"
     * )
     * @ORM\JoinColumn(
     *     name="notification_channel_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $notificationChannel;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\UserPreferences",
     *     inversedBy="notificationPreferences"
     * )
     * @ORM\JoinColumn(
     *     name="user_preferences_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $userPreferences;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\NotificationType",
     *     inversedBy="notificationPreferences"
     * )
     * @ORM\JoinColumn(
     *     name="notification_type_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $notificationType;

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
     * Set notificationChannel
     *
     * @param NotificationChannel $notificationChannel
     *
     * @return NotificationPreferences
     */
    public function setNotificationChannel(NotificationChannel $notificationChannel)
    {
        $this->notificationChannel = $notificationChannel;

        return $this;
    }

    /**
     * Get notificationChannel
     *
     * @return NotificationChannel
     */
    public function getNotificationChannel()
    {
        return $this->notificationChannel;
    }

    /**
     * Set userPreferences
     *
     * @param UserPreferences $userPreferences
     *
     * @return NotificationPreferences
     */
    public function setUserPreferences(UserPreferences $userPreferences)
    {
        $this->userPreferences = $userPreferences;

        return $this;
    }

    /**
     * Get userPreferences
     *
     * @return UserPreferences
     */
    public function getUserPreferences()
    {
        return $this->userPreferences;
    }

    /**
     * Set notificationType
     *
     * @param NotificationType $notificationType
     *
     * @return NotificationPreferences
     */
    public function setNotificationType(NotificationType $notificationType)
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * Get notificationType
     *
     * @return NotificationType
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }
}
