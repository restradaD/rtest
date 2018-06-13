<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Company
 *
 * @Gedmo\Loggable
 * @Vich\Uploadable
 * @UniqueEntity("slug")
 * @ORM\Table(name="company")
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyRepository")
 */
class Company
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
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, unique=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $enabled;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="email", type="string", length=255, nullable=true, unique=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $expired;

    /**
     * @var \DateTime
     *
     * @Assert\Date()
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $expiresAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="company")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Settings", mappedBy="company")
     */
    private $settings;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

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
     * Company logo
     *
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/jpeg", "image/pjpeg", "image/png"}
     * )
     * @Vich\UploadableField(mapping="company_logo", fileNameProperty="logo")
     *
     * @var File
     */
    private $photo;

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
     * Set name
     *
     * @param string $name
     *
     * @return Company
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
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Company
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Company
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Company
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
     * @return Company
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
     * Set email
     *
     * @param string $email
     *
     * @return Company
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return Company
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return bool
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return Company
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set Photo
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Company
     */
    public function setPhoto(File $image = null)
    {
        $this->photo = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get Company Photo
     *
     * @return File|null
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("logo")
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->settings = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return Company
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add user
     *
     * @param Settings $settings
     *
     * @return Company
     */
    public function addSettings(Settings $settings)
    {
        $this->settings[] = $settings;

        return $this;
    }

    /**
     * Remove settings
     *
     * @param Settings $settings
     */
    public function removeSettings(Settings $settings)
    {
        $this->settings->removeElement($settings);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get company notes.
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set company notes.
     * @param string $notes
     * @return Company
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get about this company.
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set about this company.
     * @param string $about
     * @return Company
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }


    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Company
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Add setting
     *
     * @param Settings $setting
     *
     * @return Company
     */
    public function addSetting(Settings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param Settings $setting
     */
    public function removeSetting(Settings $setting)
    {
        $this->settings->removeElement($setting);
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
     * @return Company
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
     * @return Company
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
     * @return Company
     */
    public function setUpdatedFromIp($updatedFromIp)
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    /**
     *
     * User initials.
     * @return string
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("initials")
     *
     * */
    public function getInitial()
    {
        $full_name = (string)$this;
        $names = explode(' ', $full_name);
        $initials = '';

        foreach ($names as $key => $name) {
            $initials .= mb_substr($name, 0, 1, 'utf-8');
        }

        return strtoupper($initials);
    }

    public function getPicture($size = '150x150')
    {
        $picture = '//dummyimage.com/'. $size .'/000/fff&text='.$this->getInitial();

        if ($this->getPhoto()) {
            $picture_path = realpath($this->getPhoto());
            $picture = str_replace(getcwd(), '', $picture_path);
        }

        return $picture;
    }
}
