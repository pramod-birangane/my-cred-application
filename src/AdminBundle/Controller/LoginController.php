<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Entity\LoginDetails;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use AdminBundle\Service\CommonFunctions;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use AdminBundle\Traits\LoginStatus;
use AdminBundle\TempClasses\ForgotPassword;
use AdminBundle\TempClasses\ChangePassword;

class LoginController extends Controller {

    use LoginStatus;
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
    public function changePasswordAction(Request $request) {
        if($this->checkLoginStatus() === false){
            return $this->redirectToRoute("login");
        }
        $objChangePassword = new ChangePassword($objChangePassword);
        $formManager = $this->createFormBuilder($objChangePassword);
        $formManager->add("old_password", TextType::class);
        $formManager->add("new_password", TextType::class);
        $formManager->add("confirm_password", TextType::class);
        $formManager->add("change_password", SubmitType::class);
        $formManager->add("reset_password", ResetType::class);
        $actualForm = $formManager->getForm();
        
        if($actualForm->handleRequest($request)){
            if($actualForm->isValid()){
                if($this->getLoggedinuserPassword() !== $objChangePassword->getOldPassword()){
                    $this->addFlash("error", "Invalid old password");
                } else if($objChangePassword->getNewPassword() !== $objChangePassword->getConfirmPassword()){
                    $this->addFlash("error", "New Password and Confirm Password should be same.");
                } else {
                    $repo = $this->getDoctrine()->getRepository(LoginDetails::class);
                    $q = $repo->createQueryBuilder('l')
                        ->update()
                        ->set('l.password', ':password')
                        ->where('l.id = :id')
                        ->setParameter('password', $objChangePassword->getNewPassword())
                        ->setParameter('id', $this->getLGID())
                        ->getQuery();
                    $q->execute();
                    $this->setLoggedinuserPassword($objChangePassword->getNewPassword());
                    $this->addFlash("success", "Password has been changed successfully.");
                }
            }
        }
        
        $htmlForm = $actualForm->createView();
        return $this->render('AdminBundle:AdminDesign:change_password.html.twig', array("form" => $htmlForm));
    }

    /**
     * @Route("/forgot-password/", name="forgot_password")
     */
    public function forgotPasswordAction(Request $request) {
        $objForgotPassword = new ForgotPassword();
        $formManager = $this->createFormBuilder($objForgotPassword);
        $formManager->add("username_email", TextType::class);
        $formManager->add("submit", SubmitType::class);
        $actualForm = $formManager->getForm();
        $actualForm->handleRequest($request);
        if($actualForm->isSubmitted()){
            if($actualForm->isValid()){
                $repo = $this->getDoctrine()->getRepository(LoginDetails::class);
                $query = $repo
                    ->createQueryBuilder('l')
                    ->where('l.username = :username_email OR l.email = :username_email')
                    ->setParameter('username_email', $objForgotPassword->getUsernameEmail())
                    ->getQuery();
                $objDBLoginDetails = $query->getResult();
                if(empty($objDBLoginDetails)){
                    $this->addFlash("error", "Invalid username/email");
                } else {
                    $mailer = $this->get("mailer");
                    $message = (new \Swift_Message('Test Mail Program'))
                                ->setSubject('Test Mail Program')
                                ->setFrom('pramodb.iprogrammer@gmail.com')
                                ->setTo('pramodb@iprogrammer.com')
                                ->setBody('Test Mail Program','text/plain');
                    if($mailer->send($message)){
                        $this->addFlash("success", "You password has been mailed to your email.");
                    } else {
                        $this->addFlash("error", "You password can not be sent to your email id at the moment.");
                    }
                }
            }
        }
        $htmlForm = $actualForm->createView();
        return $this->render('AdminBundle:AdminDesign:forgot_password.html.twig', array("form" => $htmlForm));
    }

    /**
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request) {
        
        $objUserEnteredLoginDetails = new LoginDetails();
        
        $formManager = $this->createFormBuilder($objUserEnteredLoginDetails);
        $formManager->add('username', TextType::class, array('attr' => array('maxlength' => '50')));
        $formManager->add('password', PasswordType::class, array('attr' => array('maxlength' => '50')));
        $formManager->add('remember_me', CheckboxType::class, array('label'=>'Remember Me', 'required' => false));
        $formManager->add('login', SubmitType::class, array('label'=>'Login'));
        $actualForm = $formManager->getForm();
        $actualForm->getErrors(true);
        $actualForm->handleRequest($request);
        if($actualForm->isSubmitted() ){
            if($actualForm->isValid()){
                if($this->authenticateUser($objUserEnteredLoginDetails) === true){
                    return $this->redirectToRoute("products");
                }
            }
        }
        $htmlForm = $actualForm->createView();
        return $this->render('AdminBundle:AdminDesign:login.html.twig', array('form' => $htmlForm));
    }
    
    private function initialiseUserSession(LoginDetails $objLoggedInUser){
        $session = $this->container->get('session');
        $session->set("LoggedinUserData", $objLoggedInUser);
    }

    public static function authenticateUserSession($session){
        if(!($session->has("LoggedinUserData"))){
            return false;
        } else {
            return true;
        }
    }

    private function authenticateUser(LoginDetails $userEnteredLoginDetails){
        $repository = $this->getDoctrine()->getRepository(LoginDetails::class);
        $ObjDBLoginDetails = $repository->findOneByUsername($userEnteredLoginDetails->getUsername());
        if(empty($ObjDBLoginDetails)){
            $this->addFlash("error", "Invalid Username");
            return false;
        }
        else if(
            $userEnteredLoginDetails->getUsername() === $ObjDBLoginDetails->getUsername() &&
            $userEnteredLoginDetails->getPassword() === $ObjDBLoginDetails->getPassword()
        ){
            $this->initialiseUserSession($ObjDBLoginDetails);
            return true;
         } else {
             $this->addFlash("error", "Invalid Password");
             return false;
         }
    }

    /**
     * @Route("/logout/", name="logout")
     */
    public function logoutAction() {
        
        if($this->checkLoginStatus() === true) {
            $this->addFlash("success", "You are logged out successfully.");
            $this->container->get('session')->remove("LoggedinUserData");
        }
        return $this->redirectToRoute("login");
    }

}
