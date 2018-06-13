<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRepository")
 */
class Permission implements Translatable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
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
     * @ORM\Column(name="code", type="string", length=255)
     * @Serializer\Expose()
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RolePermission", mappedBy="permission")
     * @Serializer\Expose()
     */
    private $rolePermissions;

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
     * Permission constructor.
     */
    public function __construct()
    {
        $this->rolePermissions = new ArrayCollection();
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
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Permission
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Permission
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Permission
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
     * @return Permission
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
     * Add rolePermissions
     *
     * @param RolePermission $rolePermission
     *
     * @return Permission
     */
    public function addRolePermissions(RolePermission $rolePermission)
    {
        $this->rolePermissions[] = $rolePermission;

        return $this;
    }

    /**
     * Remove rolePermission
     *
     * @param RolePermission $rolePermission
     */
    public function removeUser(RolePermission $rolePermission)
    {
        $this->rolePermissions->removeElement($rolePermission);
    }

    /**
     * Get rolePermissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRolePermissions()
    {
        return $this->rolePermissions;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    /**
     * @param $role
     * @return int
     */
    public function isGrantedForRole($role)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('role', $role));
        $matchingRolePermissions =  $this->getRolePermissions()->matching($criteria);
        return count($matchingRolePermissions);
    }
}
