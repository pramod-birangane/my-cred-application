<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
    /**
     * @Route("/change-password/", name="change_password")
     */
    public function changePasswordAction() {
        return $this->render('AdminBundle:AdminDesign:change_password.html.twig');
    }
    
    /**
     * @Route("/forgot-password/", name="forgot_password")
     */
    public function forgotPasswordAction(){
        return $this->render('AdminBundle:AdminDesign:forgot_password.html.twig');
    }

        /**
     * @Route("/login/", name="login")
     */
    public function loginAction()
    {
        return $this->render('AdminBundle:AdminDesign:login.html.twig');
    }
    
    /**
     * @Route("/logout/", name="logout")
     */
    public function logoutAction() {
        die("I have logged out");
    }
}
