<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\PhoneNumber;
use App\Entity\Orders;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Repository\PhoneNumberRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductRepository;
use App\Security\CustomerAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * @Route("/")
 * 
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customer_index", methods={"GET"})
     */
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/homePage", name="home_page", methods={"GET"})
     */
    public function homePage(UserInterface $user)
    {
        return $this->redirectToRoute('customer_account');
    }

    /**
     * @Route("/customerHome", name="customer_home", methods={"GET"})
     */
    public function customerHome(ProductRepository $productRepository)
    {
        return $this->render('customer/home.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/account", name="customer_account", methods={"GET"})
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function account(UserInterface $user, OrdersRepository $ordersRepository)
    {
        $customer_id = $user->getId();
        $order_details = $ordersRepository->getCustomerOrders($customer_id);
        
        $all_orders = [];
        if($order_details){
        $order = [];
        $order_id = $order_details[0]['id'];
        $order['order_id'] = $order_details[0]['id'];
        $order['status'] = $order_details[0]['order_status'];
        $order['date_placed'] = $order_details[0]['date_placed'];
        $products = [];
        foreach($order_details as $product){
            $arr = [];
            if($product['id']==$order_id){
                $arr['pr_name'] = $product['name'];
                $arr['quantity'] = $product['quantity'];
                $arr['unit_price'] = $product['unit_price'];
                array_push($products,$arr);
            }
            else{
                $order['products'] = $products;
                $products = [];
                array_push($all_orders,$order);
                $order = [];
                $order_id = $product['id'];
                $order['order_id'] = $product['id'];
                $order['status'] = $product['order_status'];
                $order['date_placed'] = $product['date_placed'];

                $arr['pr_name'] = $product['name'];
                $arr['quantity'] = $product['quantity'];
                $arr['unit_price'] = $product['unit_price'];
                array_push($products,$arr);
            }
        }
        $order['products'] = $products;
        array_push($all_orders,$order);
    }
        return $this->render('customer/customerAccount.html.twig',['orders'=>$all_orders]);
    }


    /**
     * @Route("/register", name="customer_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,  
                CustomerAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler,
                PhoneNumberRepository $phoneNumberRepository)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $password = $passwordEncoder->encodePassword($customer, $customer->getPlainPassword());
            $customer->setPassword($password);
            $customer->setRoles(array('ROLE_CUSTOMER'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $phoneNumberRepository->insertPNum($customer->getPhoneNumber(), $customer->getId());


            return $guardHandler->authenticateUserAndHandleSuccess(
                    $customer,
                    $request,
                    $authenticator,
                    'customer_users'
                );
        }

        return $this->render(
            'registration/registerCustomer.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/login", name="login_customer")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/customerLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout_customer")
     */
    public function logout()
    {
        return $this->redirectToRoute('product_display');
    }

    /**
     * @Route("/new", name="customer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('customer_index');
        }

        return $this->render('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_show", methods={"GET"})
     */
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Customer $customer): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_index');
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Customer $customer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_index');
    }

    
}
