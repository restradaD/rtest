<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Settings
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Settings
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company", inversedBy="settings")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */

    private $company;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @Assert\Length(min=2, max=255)
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Url(protocols = {"http", "https"})
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     * @Assert\Url(protocols = {"http", "https"})
     * @ORM\Column(name="api_url", type="string", length=255, nullable=true)
     */
    private $apiUrl;


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
     * Set host
     *
     * @param string $url
     *
     * @return Settings
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
     * Set apiUrl
     *
     * @param string $apiUrl
     *
     * @return Settings
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Get apiUrl
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Settings
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
     * @return Settings
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
}
