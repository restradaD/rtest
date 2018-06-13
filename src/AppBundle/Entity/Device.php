<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Device
 *
 * @ORM\Table(name="device")
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviceRepository")
 */
class Device
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
     *     targetEntity="AppBundle\Entity\User",
     *     inversedBy="devices"
     * )
     * @ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="os", type="string", length=150, nullable=true)
     */
    private $os;

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
     * @var string $createdFromIp
     *
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(length=45, nullable=true)
     */
    private $createdFromIp;

    /**
     * @var string $updatedFromIp
     *
     * @Gedmo\IpTraceable(on="update")
     * @ORM\Column(length=45, nullable=true)
     */
    private $updatedFromIp;


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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Device
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set os
     *
     * @param string $os
     *
     * @return Device
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Device
     */
    public function setUser(User $user)
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Device
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
     * @return Device
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
     * Get Deleted At
     * @return \DateTime
     * */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set Deleted At
     * @param \DateTime
     * @return Device
     * */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Get Ip entity was created.
     * @return string
     * */
    public function getCreatedFromIp()
    {
        return $this->createdFromIp;
    }

    /**
     * Get Ip entity was updated
     * @return string
     * */
    public function getUpdatedFromIp()
    {
        return $this->updatedFromIp;
    }


    /**
     * Set createdFromIp
     *
     * @param string $createdFromIp
     *
     * @return Device
     */
    public function setCreatedFromIp($createdFromIp)
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    /**
     * Set updatedFromIp
     *
     * @param string $updatedFromIp
     *
     * @return Device
     */
    public function setUpdatedFromIp($updatedFromIp)
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }
}
