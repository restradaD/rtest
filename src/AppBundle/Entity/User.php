<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity
 * @Gedmo\Loggable
 * @Vich\Uploadable
 * @ORM\Table(name="`user`")
 * @UniqueEntity({ "username", "email" })
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 */
class User extends BaseUser
{
    const ROLES_DEFINITION = [
        'ROLE_USER' => 'app.roles.user',
        'ROLE_APP' => 'app.roles.app',
        'ROLE_ADMIN' => 'app.roles.admin',
        'ROLE_API' => 'app.roles.api',
        'ROLE_SUPER_ADMIN' => 'app.roles.super_admin',
        'ROLE_TRANSLATOR' => 'app.roles.translator',
    ];

    const SUPER_ADMIN_EXCLUSIVES_ROLES = [
        'ROLE_SUPER_ADMIN',
        'ROLE_API',
        'ROLE_TRANSLATOR'
    ];

    const ROLE_USER = 'ROLE_USER';
    const ROLE_APP = 'ROLE_APP';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_API = 'ROLE_API';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_TRANSLATOR = 'ROLE_TRANSLATOR';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @Assert\NotBlank(groups={"create", "edit"})
     * @ORM\Column(type="string", name="first_name", length=150, nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     * */
    private $firstName;

    /**
     * @Assert\NotBlank(groups={"create", "edit"})
     * @ORM\Column(type="string", name="last_name", length=150, nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     * */
    private $lastName;

    /**
     * @ORM\Column(type="string", name="api_key", length=250, nullable=true)
     * @Gedmo\Versioned
     * @Serializer\Expose()
     * */
    private $apiKey;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="users")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Gedmo\Versioned
     */
    private $company;

    /** @var string $role */
    public $pre_role;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Length(
     *      min = 2,
     *      max = 4096,
     *      minMessage = "fos_user.password.short",
     *      maxMessage = "fos_user.password.long",
     *      groups = { "create" }
     * )
     * @var string $passcode
     * */
    public $passcode;

    /**
     * Profile picture property
     *
     * @Vich\UploadableField(mapping="profile_picture", fileNameProperty="profile_picture")
     *
     * @var File
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     *
     */
    private $profile_picture;

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
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Serializer\Expose()
     * @Gedmo\Versioned
     * */
    private $locale;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Notification",
     *     mappedBy="from"
     * )
     */
    private $notificationFrom;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Notification",
     *     mappedBy="to"
     * )
     */
    private $notificationTo;

    /**
     * @ORM\OneToOne(
     *     targetEntity="AppBundle\Entity\UserPreferences",
     *     inversedBy="user"
     * )
     * @ORM\JoinColumn(name="user_preference_id", referencedColumnName="id", unique=true)
     */
    private $preferences;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Device",
     *     mappedBy="user"
     * )
     */
    private $devices;

    /**
     * Set User profile picture
     * @param string $imageName
     * @return User
     */
    public function setProfilePicture($imageName)
    {
        $this->profile_picture = $imageName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfilePicture()
    {
        return $this->profile_picture;
    }

    /**
     * Set Profile picture
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return User
     */
    public function setPhoto(File $image = null)
    {
        $this->photo = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->lastLogin = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPhoto()
    {
        return $this->photo;
    }


    public function __construct()
    {
        parent::__construct();

        $this->notificationTo = new ArrayCollection();
        $this->notificationFrom = new ArrayCollection();

        $this->devices = new ArrayCollection();
    }

    public function __toString()
    {
        return ucwords($this->getFullName() ? $this->getFullName() : $this->getUsername());
    }

    /**
     * Get first name.
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * set first name.
     * @param mixed $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get last name.
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set last name.
     * @param mixed $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get full name
     * @return mixed
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("full_name")
     *
     * */
    public function getFullName()
    {
        return ($this->firstName && $this->lastName) ? $this->firstName . ' ' . $this->lastName : false;
    }

    /**
     * Get primary role.
     * @return string
     * */
    public function getRole()
    {
        $roleConstant = $this->getRoleConstant() ? $this->getRoleConstant() : User::ROLE_USER;
        return User::ROLES_DEFINITION[$roleConstant] ? User::ROLES_DEFINITION[$roleConstant] : 'Unknown';
    }

    public function getRoleConstant()
    {
        return isset($this->roles[0]) ? $this->roles[0] : User::ROLE_USER;
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

    /**
     * Return user Profile Picture.
     * @param string $size = '150x150'
     * @return mixed
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("picture")
     * */
    public function getPicture($size = '150x150')
    {
        $picture = '//dummyimage.com/'. $size .'/000/fff&text='.$this->getInitial();

        if ($this->getProfilePicture()) {
            $picture_path = realpath($this->getPhoto());
            $picture = str_replace(getcwd(), '', $picture_path);
        }

        return $picture;
    }

    /**
     * Get Company.
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set Company.
     * @param mixed $company
     * @return User
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
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
     * @return User
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
     * @return User
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
     * @return User
     */
    public function setUpdatedFromIp($updatedFromIp)
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    /**
     * Get User Api Key
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set User Api Key
     * @param mixed $apiKey
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Add notificationFrom
     *
     * @param Notification $notificationFrom
     *
     * @return User
     */
    public function addNotificationFrom(Notification $notificationFrom)
    {
        $this->notificationFrom[] = $notificationFrom;

        return $this;
    }

    /**
     * Remove notificationFrom
     *
     * @param Notification $notificationFrom
     */
    public function removeNotificationFrom(Notification $notificationFrom)
    {
        $this->notificationFrom->removeElement($notificationFrom);
    }

    /**
     * Get notificationFrom
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationFrom()
    {
        return $this->notificationFrom;
    }

    /**
     * Add notificationTo
     *
     * @param Notification $notificationTo
     *
     * @return User
     */
    public function addNotificationTo(Notification $notificationTo)
    {
        $this->notificationTo[] = $notificationTo;

        return $this;
    }

    /**
     * Remove notificationTo
     *
     * @param Notification $notificationTo
     */
    public function removeNotificationTo(Notification $notificationTo)
    {
        $this->notificationTo->removeElement($notificationTo);
    }

    /**
     * Get notificationTo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotificationTo()
    {
        return $this->notificationTo;
    }

    /**
     * Set preferences
     *
     * @param UserPreferences $preferences
     *
     * @return User
     */
    public function setPreferences(UserPreferences $preferences = null)
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * Get preferences
     *
     * @return UserPreferences
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Add device
     *
     * @param Device $device
     *
     * @return User
     */
    public function addDevice(Device $device)
    {
        $this->devices[] = $device;

        return $this;
    }

    /**
     * Remove device
     *
     * @param Device $device
     */
    public function removeDevice(Device $device)
    {
        $this->devices->removeElement($device);
    }

    /**
     * Get devices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevices()
    {
        return $this->devices;
    }
}
