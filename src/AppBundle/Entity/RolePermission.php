<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RolePermission
 *
 * @ORM\Table(name="role_permission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RolePermissionRepository")
 */
class RolePermission
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
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=150)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Permission", inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     */
    private $permission;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


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
     * Set role
     *
     * @param string $role
     *
     * @return RolePermission
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return RolePermission
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
     * @return RolePermission
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
     * Get Permission
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set permission
     * @param mixed $permission
     * @return RolePermission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    public function __toString()
    {
        return $this->getRole() . ' ' . $this->getPermission();
    }
}
