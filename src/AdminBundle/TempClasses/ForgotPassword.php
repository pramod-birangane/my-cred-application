<?php
namespace AdminBundle\TempClasses;

class ForgotPassword
{
    /**
     * @Assert\NotBlank(message="Please enter Username/Email")
     */
    private $username_email;
    
    public function getUsernameEmail() {
        return $this->username_email;
    }
    
    public function setUsernameEmail($username_email) {
        $this->username_email = $username_email;
    }
}