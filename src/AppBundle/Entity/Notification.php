<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notification
 *
 * @Gedmo\Loggable
 * @ORM\Table(name="notification")
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var int
     *
     * @ORM\Id
     * @Serializer\Expose()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\NotificationType",
     *     inversedBy="notifications"
     * )
     * @ORM\JoinColumn(
     *     name="notification_type_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     *
     * @Serializer\Expose()
     */
    private $notificationType;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="notificationFrom"
     * )
     * @ORM\JoinColumn(
     *     name="from_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     * @Serializer\Expose()
     */
    private $from;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="notificationTo"
     * )
     * @ORM\JoinColumn(
     *     name="to_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     * @Serializer\Expose()
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     */
    private $path;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="array", nullable=true)
     * @Serializer\Expose()
     */
    private $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     */
    private $url;

    /**
     * @var bool
     *
     * @ORM\Column(name="seen", type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private $seen;

    /**
     * @var bool
     *
     * @ORM\Column(name="checked", type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private $checked;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private $response;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\NotificationChannel", inversedBy="notifications")
     * @ORM\JoinTable(
     *     name="notifications_has_channels",
     *     joinColumns={
     *         @ORM\JoinColumn(
     *             name="notification_id",
     *             referencedColumnName="id",
     *             nullable=false
     *         )
     *     },
     *     inverseJoinColumns={@ORM\JoinColumn(name="notification_channel_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $notificationChannels;


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
     * Set title
     *
     * @param string $title
     *
     * @return Notification
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Notification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Notification
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return Notification
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Notification
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set seen
     *
     * @param boolean $seen
     *
     * @return Notification
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * Get seen
     *
     * @return bool
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     *
     * @return Notification
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return bool
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set response
     *
     * @param string $response
     *
     * @return Notification
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Add notificationChannel
     *
     * @param NotificationChannel $notificationChannel
     *
     * @return Notification
     */
    public function addNotificationChannel(NotificationChannel $notificationChannel)
    {
        $this->notificationChannels[] = $notificationChannel;

        return $this;
    }

    /**
     * Remove notificationChannel
     *
     * @param NotificationChannel $notificationChannel
     */
    public function removeNotificationChannel(NotificationChannel $notificationChannel)
    {
        $this->notificationChannels->removeElement($notificationChannel);
    }

    /**
     * Get notificationChannels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationChannels()
    {
        return $this->notificationChannels;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notificationChannels = new ArrayCollection();
    }

    /**
     * Set notificationType
     *
     * @param NotificationType $notificationType
     *
     * @return Notification
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


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Notification
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
     * @return Notification
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
     * @return Notification
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
     * Set from
     *
     * @param User $from
     *
     * @return Notification
     */
    public function setFrom(User $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return User
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param User $to
     *
     * @return Notification
     */
    public function setTo(User $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return User
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->title;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $notificationChannels
     * @throws \Exception
     */
    public function setNotificationChannels($notificationChannels)
    {
        $this->notificationChannels = $notificationChannels;
    }

    /**
     * Get Text class
     *
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("text_class")
     */
    public function getTextClass()
    {
        $class = 'muted';

        if (!$this->seen && !$this->checked) {
            $class = 'navy';
        }

        if ($this->seen) {
            $class = 'warning';
        }

        if ($this->checked) {
            $class = 'muted';
        }

        return $class;
    }

    /**
     * Get active class
     *
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("active_class")
     */
    public function getActiveClass()
    {
        return ($this->seen) ? '' : 'active';
    }
}
