<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index", methods={"GET"})
     *
     *
     *
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/all", name="product_display", methods={"GET"})
     *
     */
    public function allProducts(ProductRepository $productRepository): Response
    {
        return $this->render('product/all_products.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     * 
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function cart()
    {
        return $this->render('product/cart.html.twig');

    }

    /**
     * @Route("/addToCart/{id}", name="add_to_cart", methods={"GET"})
     * 
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function addToCart(Product $product): Response
    {
        return $this->render('product/cart.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * @Route("/manager/product/new", name="product_new", methods={"GET","POST"})
     *@IsGranted("ROLE_MANAGER")
     *
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/manager/product/{id}/edit", name="product_edit", methods={"GET","POST"})
     *
     *@IsGranted("ROLE_MANAGER")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/manager/product/{id}", name="product_delete", methods={"DELETE"})
//     *
//     *@IsGranted("ROLE_MANAGER")
//     */
//    public function delete(Request $request, Product $product): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($product);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('product_index');
//    }
}
