<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Entity\LoginDetails;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class LoginController extends Controller {

    /**
     * @Route("/create-root-user/", name="create_root_user")
     */
    public function createRootUser(Request $request) {
        $rootUser = new LoginDetails();
        $form = $this->createFormBuilder($rootUser)
        ->add('email', EmailType::class)
        ->add('username', TextType::class)
        ->add('password', PasswordType::class)
        ->add('create root user', SubmitType::class)
        ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $rootUser->setCreatedatetime(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rootUser);
            try {
                $entityManager->flush();
            }
            catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'email and/or username already exists');
                return $this->render("AdminBundle:AdminDesign:create_root_user.html.twig", array("create_root_user_form" => $form->createView()));
            }
            
            $this->addFlash('success', 'root user created successfully');
            return $this->render("AdminBundle:AdminDesign:create_root_user_message.html.twig");
        }

        return $this->render("AdminBundle:AdminDesign:create_root_user.html.twig", array("create_root_user_form" => $form->createView() ));
    }

    private function authenticateHttpRequest($request) {
        if (
                $request->headers->has("php-auth-user") &&
                $request->headers->has("php-auth-pw") &&
                !empty($request->headers->get("php-auth-user")) &&
                !empty($request->headers->get("php-auth-pw")) &&
                $request->headers->get("php-auth-user") === 'pramod' &&
                $request->headers->get("php-auth-pw") === 'pramod123' &&
                ( $request->getMethod() == 'POST' || $request->getMethod() == 'GET')
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @Route("/change-password/", name="change_password")
     */
    public function changePasswordAction() {
        return $this->render('AdminBundle:AdminDesign:change_password.html.twig');
    }

    /**
     * @Route("/forgot-password/", name="forgot_password")
     */
    public function forgotPasswordAction() {
        return $this->render('AdminBundle:AdminDesign:forgot_password.html.twig');
    }

    /**
     * @Route("/login/", name="login")
     */
    public function loginAction() {
        return $this->render('AdminBundle:AdminDesign:login.html.twig');
    }

    /**
     * @Route("/logout/", name="logout")
     */
    public function logoutAction() {
        die("I have logged out");
    }

}
