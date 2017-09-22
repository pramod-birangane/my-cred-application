<?php
namespace AdminBundle\Traits;
trait LoginStatus
{
    private $sessionData = null;
    
    private function initSessionVar(){
        if(is_null($this->sessionData)){
            $this->sessionData = $this->container->get("session");
        }
    }

    private function checkLoginStatus(){
        $this->initSessionVar();
        if(!($this->sessionData->has("LoggedinUserData"))){
            $this->addFlash("error", "Unauthorised access");
            return false;
        } else {
            return true;
        }
    }
    
    private function getLoggedinuserPassword(){
        $this->initSessionVar();
        return $this->sessionData->get("LoggedinUserData")->getPassword();
    }
    
    private function setLoggedinuserPassword($password){
        $this->initSessionVar();
        $this->sessionData->get("LoggedinUserData")->setPassword($password);
    }
    
    private function getLoggedinuserObject(){
        $this->initSessionVar();
        return $this->sessionData->get("LoggedinUserData");
    }
    
    private function getLGID(){
        $this->initSessionVar();
        return $this->sessionData->get("LoggedinUserData")->getId();
    }
}

