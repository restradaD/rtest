<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * NotificationType
 *
 * @Gedmo\Loggable
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Table(name="notification_type")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationTypeRepository")
 */
class NotificationType implements Translatable
{
    const TASK_CREATED = 1;
    const TASK_UPDATED = 2;
    const WORKFLOW_CREATED = 3;
    const WORKFLOW_UPDATED = 4;
    const CASE_CREATED = 5;
    const CASE_UPDATED = 6;
    const CASE_CHANGE_OF_STATUS = 7;
    const CASE_NOTE_CREATED = 8;
    const DOCUMENT_UPLOADED = 9;
    const DOCUMENT_DELETED = 10;

    /**
     * @var int
     *
     * @ORM\Id
     * @Serializer\Expose()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="integer")
     *
     */
    private $id;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Serializer\Expose()
     * @Gedmo\Translatable
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="text_template", type="text")
     * @Serializer\Expose()
     * @Gedmo\Translatable
     */
    private $textTemplate;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Serializer\Expose()
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Notification",
     *     mappedBy="notificationType"
     * )
     */

    private $notifications;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\NotificationPreferences",
     *     mappedBy="notificationType"
     * )
     */
    private $notificationPreferences;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return NotificationType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


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
     * Set name
     *
     * @param string $name
     *
     * @return NotificationType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->notificationPreferences = new ArrayCollection();
    }

    /**
     * Add notification
     *
     * @param Notification $notification
     *
     * @return NotificationType
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param Notification $notification
     */
    public function removeNotification(Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return NotificationType
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return NotificationType
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return NotificationType
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * Add notificationPreferences
     *
     * @param NotificationPreferences $notificationPreferences
     *
     * @return NotificationType
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



    /**
     * Set textTemplate
     *
     * @param string $textTemplate
     *
     * @return NotificationType
     */
    public function setTextTemplate($textTemplate)
    {
        $this->textTemplate = $textTemplate;

        return $this;
    }

    /**
     * Get textTemplate
     *
     * @return string
     */
    public function getTextTemplate()
    {
        return $this->textTemplate;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
