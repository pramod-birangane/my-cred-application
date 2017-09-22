<?php
namespace AdminBundle\TempClasses;
use Symfony\Component\Validator\Constraints as Assert;
class ChangePassword
{
    /**
     * @Assert\NotBlank(message="Please enter old password")
     */
    private $old_password;
    /**
     * @Assert\NotBlank(message="Please enter new password")
     */
    private $new_password;
    /**
     * @Assert\NotBlank(message="Please enter confirm password")
     */
    private $confirm_password;
    
    public function getOldPassword() {
        return $this->old_password;
    }
    
    public function setOldPassword($old_password) {
        $this->old_password = $old_password;
    }
    
    public function getNewPassword() {
        return $this->new_password;
    }
    
    public function setNewPassword($new_password) {
        $this->new_password = $new_password;
    }
    
    public function getConfirmPassword() {
        return $this->confirm_password;
    }
    
    public function setConfirmPassword($confirm_password) {
        $this->confirm_password = $confirm_password;
    }
}
