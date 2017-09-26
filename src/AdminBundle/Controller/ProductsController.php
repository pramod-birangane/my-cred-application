<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Traits\LoginStatus;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use AdminBundle\Entity\Products;

class ProductsController extends Controller {

    use LoginStatus;
    /**
     * @Route("/products/", name="products")
     */
    public function productsAction() {
        if($this->checkLoginStatus() === false){
            return $this->redirectToRoute("login");
        }
        return $this->render("AdminBundle:AdminDesign:products.html.twig");
    }

    /**
     * @Route("/product/add/", name="add_product")
     */
    public function addProductAction(Request $request) {
        if($this->checkLoginStatus() === false){
            return $this->redirectToRoute("login");
        }
        $product = new Products();
        $form = $this->createFormBuilder($product)
                ->add("name", TextType::class, array('label'=>'Product Name'))
                ->add("price", TextType::class, array('label'=>'Product Price'))
                ->add("description", TextareaType::class, array('label'=>'Product Description'))
                ->add("photo", FileType::class, array('label'=>'Product Photo'))
                ->add("submit", SubmitType::class, array('label'=>'Add Product'))
                ->add("reset", ResetType::class, array('label'=>'Reset Product'))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $uploadedPhoto = $product->getPhoto();
                $possibleExtension = $product->getPhoto()->guessExtension();
                if(!($possibleExtension == 'jpg' or $possibleExtension == 'png' or $possibleExtension == 'gif')){
                    $this->addFlash("error", "Invalid image file");
                } else {
                    $newFilename = md5(uniqid()).".".$possibleExtension;
                    $pathToUpload = realpath('../web/uploads/');
                    $uploadedPhoto->move($pathToUpload, $newFilename);
                }
            }
        }
        return $this->render("AdminBundle:AdminDesign:add_product.html.twig", array("form" => $form->createView()));
    }

    /**
     * @Route("/product/edit/", name="edit_product_withoutparam")
     * @Route("/product/edit/{productId}", name="edit_product_withparam")
     */
    public function editProductAction($productId = null) {
        if ($productId === null) {
            return $this->render("AdminBundle:AdminDesign:message.html.twig");
        } else {
            return $this->render("AdminBundle:AdminDesign:edit_product.html.twig");
        }
    }

}
