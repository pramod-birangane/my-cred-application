<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Traits\LoginStatus;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        $repo = $this->getDoctrine()->getRepository(Products::class);
        $products = $repo->findAll();
        return $this->render("AdminBundle:AdminDesign:products.html.twig", array("products" => $products));
    }

    /**
     * @Route("/product/add/", name="add_product")
     * @Route("/product/edit", name="edit_product")
     * @Route("/product/edit/{productId}", name="edit_product")
     */
    public function addProductAction(Request $request, $productId=null) {
        
        if($this->checkLoginStatus() === false){
            return $this->redirectToRoute("login");
        }
        $data = array();
        if(is_null($productId)){
            $product = new Products();
        } else {
            $repo = $this->getDoctrine()->getRepository(Products::class);
            $product = $repo->find($productId);
            $data['product'] = $product;
            if(is_null($product)){
                $this->addFlash("error", "Product does not exists.");
                $product = new Products();
            }
        }
        $form = $this->createFormBuilder($product)
                ->add("id", HiddenType::class)
                ->add("name", TextType::class, array('label'=>'Product Name'))
                ->add("price", TextType::class, array('label'=>'Product Price'))
                ->add("description", TextareaType::class, array('label'=>'Product Description'))
                ->add("photo", FileType::class, array('label'=>'Product Photo', 'data_class' => NULL))
                ->add("submit", SubmitType::class, array('label'=>'Add Product'))
                ->add("reset", ResetType::class, array('label'=>'Reset Product'))
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $uploadedPhoto = $product->getPhoto();
                $possibleExtension = $product->getPhoto()->guessExtension();
                if(!(
                        $possibleExtension == 'jpg' or
                        $possibleExtension == 'png' or
                        $possibleExtension == 'gif'
                    )){
                    $this->addFlash("error", "Invalid image file");
                } else {
                    $newFilename = md5(uniqid()).".".$possibleExtension;
                    $uploadedPhoto->move($this->getParameter("upload_directory"), $newFilename);
                    $product->setPhoto($newFilename);
                    
                    if(empty($product->getId())){
                        $product->setFirstCreated(new \DateTime());
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($product);
                        $em->flush();
                        $this->addFlash("success", "New product added successfully.");
                    } else {
                        $product->setLastUpdated(new \DateTime());
                        $repo = $this->getDoctrine()->getRepository(Products::class);
                        $q = $repo->createQueryBuilder('p')
                        ->update()
                        ->set('p.name', ':name')
                        ->set('p.description', ':description')
                        ->set('p.photo', ':photo')
                        ->set('p.price', ':price')
                        ->set('p.lastUpdated', ':lastUpdated')
                        ->where('p.id = :id')
                        ->setParameter('name', $product->getName())
                        ->setParameter('description', $product->getDescription())
                        ->setParameter('photo', $product->getPhoto())
                        ->setParameter('price', $product->getPrice())
                        ->setParameter('lastUpdated', $product->getLastUpdated())
                        ->setParameter('id', $product->getId())
                        ->getQuery();
                        $q->execute();
                        $this->addFlash("success", "Product updated successfully.");
                    }
                }
            }
        }
        return $this->render("AdminBundle:AdminDesign:add_product.html.twig", array("form" => $form->createView(), "data" => $data));
    }
}
