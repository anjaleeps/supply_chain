<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\DriverAssistant;
use App\Entity\Manager;
use App\Entity\Orders;
use App\Entity\StoreManager;
use App\Repository\ProductRepository;
use App\Entity\OrderProduct;
use App\Entity\Customer;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\OrderProductRepository;
use App\Repository\RouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/")
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
     * @Route("orders/checkout/{id}", name="order_checkout", methods={"GET","POST"})
     */
    public function checkout(Customer $customer,Request $request,RouteRepository $routeRepository)
    {
        $customer_id = $customer->getId();
        $routes = $routeRepository->getCustomerRoutes($customer_id);
        return $this->render('orders/place_order.html.twig',[
            'routes' => $routes
        ]);    
    }

    /**
     * @Route("orders/placeOrder/{id}", name="place_order", methods={"GET","POST"})
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function place_order(Customer $customer, Request $request, OrdersRepository $ordersRepository, OrderProductRepository $orderProductRepository)
    {
        $_details = $request->request->get('details');
        $details = json_decode($_details,true);
        
        $customer_id = $customer->getId();
        $route_id = $details['route_id'];
        $status = "placed";
        $date = date('Y/m/d');
        $orders_id = $ordersRepository->placeOrder($customer_id,$route_id,$status,$date);

        foreach ($details['products'] as $item){
            $orderProductRepository->orderProducts($orders_id, $item['id'], $item['quantity']);
        }
        return new JsonResponse('success');
    }


    /**
     * @Route("manager/orders/{id}", name="orders_show", methods={"GET"})
     * 
     * @IsGranted({"ROLE_MANAGER", "ROLE_STORE_MANAGER"})
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



    
}
