<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Traits\LoginStatus;

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
    public function addProductAction() {
        return $this->render("AdminBundle:AdminDesign:add_product.html.twig");
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
