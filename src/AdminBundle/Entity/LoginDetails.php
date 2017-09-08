<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginDetails
 *
 * @ORM\Table(name="login_details")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\LoginDetailsRepository")
 */
class LoginDetails
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdatetime", type="datetime")
     */
    private $createdatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdateddatetime", type="datetime")
     */
    private $lastupdateddatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastlogin", type="datetime")
     */
    private $lastlogin;


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
     * Set email
     *
     * @param string $email
     *
     * @return LoginDetails
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
     * Set username
     *
     * @param string $username
     *
     * @return LoginDetails
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return LoginDetails
     */
    public function setPassword($password)
    {
        $this->password = base64_encode($password);

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        
        return base64_decode($this->password);
    }

    /**
     * Set createdatetime
     *
     * @param \DateTime $createdatetime
     *
     * @return LoginDetails
     */
    public function setCreatedatetime($createdatetime)
    {
        $this->createdatetime = $createdatetime;

        return $this;
    }

    /**
     * Get createdatetime
     *
     * @return \DateTime
     */
    public function getCreatedatetime()
    {
        return $this->createdatetime;
    }

    /**
     * Set lastupdateddatetime
     *
     * @param \DateTime $lastupdateddatetime
     *
     * @return LoginDetails
     */
    public function setLastupdateddatetime($lastupdateddatetime)
    {
        $datetime = new \DateTime();
        $this->lastupdateddatetime = $datetime->format("Y-m-d H:i:s");
        
        return $this;
    }

    /**
     * Get lastupdateddatetime
     *
     * @return \DateTime
     */
    public function getLastupdateddatetime()
    {
        return $this->lastupdateddatetime;
    }

    /**
     * Set lastlogin
     *
     * @param \DateTime $lastlogin
     *
     * @return LoginDetails
     */
    public function setLastlogin($lastlogin)
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    /**
     * Get lastlogin
     *
     * @return \DateTime
     */
    public function getLastlogin()
    {
        return $this->lastlogin;
    }
}

