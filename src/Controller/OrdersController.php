<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\DriverAssistant;
use App\Entity\Manager;
use App\Entity\Orders;
use App\Entity\StoreManager;
use App\Repository\ProductRepository;
use App\Entity\OrderProduct;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\OrderProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/orders")
 */
class OrdersController extends AbstractController
{
    // /**
    //  * @Route("/", name="orders_index", methods={"GET"})
    //  */
    // public function index(OrdersRepository $ordersRepository): Response
    // {
    //     return $this->render('orders/index.html.twig', [
    //         'orders' => $ordersRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("/checkout", name="order_checkout", methods={"GET","POST"})
     */
    public function checkout(Request $request)
    {
        return $this->render('orders/customer_order.html.twig',[
            'details' => $request->request->get('details')
        ]);
    }

    /**
     * @Route("/placeOrder", name="place_order", methods={"GET","POST"})
     */
    public function place_order(OrdersRepository $ordersRepository, OrderProductRepository $orderProductRepository)
    {

        
        

        $customer_id = 1;
        $route_id = 1;
        $status = "placed";
        $details = [[1,2],[4,5]];

        $date = date('Y/m/d');
        $orders_id = $ordersRepository->placeOrder($customer_id,$route_id,$status,$date);
        foreach ($details as $item){
            $orderProductRepository->orderProducts($orders_id, $item[0], $item[1]);
        }

    }


    /**
     * @Route("/new", name="orders_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $order = new Orders();
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setOrderStatus('Placed');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('orders_index');
        }
        return $this->render('orders/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
   
    }

    /**
     * @Route("/{id}", name="orders_show", methods={"GET"})
     * 
     * @IsGranted({"ROLE_MANAGER", "ROLE_DRIVER", "ROLE_DRIVER_ASSISTANT", "ROLE_STORE_MANAGER"})
     */
    public function show(string $id, OrdersRepository $ordersRepository, ProductRepository $productRepository): Response
    {
         {
            $orderData = $ordersRepository->getOrderById($id);
            $products = $productRepository->getOrderProducts($id);
            $order = $orderData[0];
            $order['products'] = $products;
            $price = 0;

            foreach ($products as $product) {
                $price += $product['price'] * $product['quantity'];
            }
            $order['price'] = $price;

            return $this->render('orders/show.html.twig', [
                'order' => $order,
            ]);
        }

    }

    /**
     * @Route("/{id}/edit", name="orders_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Orders $order): Response
    {
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_index');
        }

        return $this->render('orders/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="orders_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Orders $order): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('orders_index');
    }


    
}
