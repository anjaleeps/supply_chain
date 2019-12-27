<?php

namespace App\Controller;

use App\Entity\StoreManager;
use App\Form\StoreManagerType;
use App\Repository\DriverAssistantRepository;
use App\Repository\DriverRepository;
use App\Repository\OrdersRepository;
use App\Repository\StoreManagerRepository;
use App\Repository\TransportsRepository;
use App\Repository\TruckRepository;
use App\Security\StoreManagerAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * @Route("/store_manager")
 */
class StoreManagerController extends AbstractController
{
    /**
     * @Route("/", name="store_manager_index", methods={"GET"})
     */
    public function index(StoreManagerRepository $storeManagerRepository): Response
    {
        return $this->render('store_manager/index.html.twig', [
            'store_managers' => $storeManagerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/register", name="store_manager_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, StoreManagerAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $storeManager = new StoreManager();
        $form = $this->createForm(StoreManagerType::class, $storeManager);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($storeManager, $storeManager->getPlainPassword());
            $storeManager->setPassword($password);
            $storeManager->setRoles(array('ROLE_STORE_MANAGER'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($storeManager);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $storeManager,
                $request,
                $authenticator,
                'store_manager_users'
            );
        }

        return $this->render(
            'registration/registerStoreManager.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/login", name="login_store_manager")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/storeManagerLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout_store_manager")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/dashboard", name="store_manager_dashboard", methods={"GET"})
     * 
     * @IsGranted("ROLE_STORE_MANAGER")
     */
    public function renderDashboard(OrdersRepository $ordersRepository, TransportsRepository $transportsRepository,
                    DriverRepository $driverRepository, DriverAssistantRepository $driverAssistantRepository, 
                    TruckRepository $truckRepository)
    {
        $id = $this->getUser()->getId();
        $trainData = $transportsRepository->getExpectedTrains($id);
        $orderData = $ordersRepository->getStoredOrders($id);
        $driverData = $driverRepository->getAvailableDrivers($id);
        $driverAssistantData = $driverAssistantRepository->getAvailableAssistants($id);
        $truckData = $truckRepository->getAvailableTrucks($id);
        $data = [];

        foreach ($orderData as $order){
            if (!(array_key_exists($order['route_id'], $data))){
                $data[$order['route_id']] = [];
            }
            if (!(array_key_exists($order['order_id'], $data[$order['route_id']]))){
                $data[$order['route_id']][$order['order_id']] = [
                    'first_name' => $order['first_name'],
                    'last_name' => $order['last_name'],
                    'products' => []
                ];
            } 

            array_push($data[$order['route_id']][$order['order_id']]['products'], [
                'name'=> $order['product_name'],
                'quantity' => $order['quantity']
            ]);
        }

        return $this->render('store_manager/dashboard.html.twig', [
            'trains' => $trainData,
            'orders' => $data,
            'drivers' => $driverData,
            'assistants' => $driverAssistantData,
            'trucks' => $truckData
        ]);
    }

    /**
     * @Route("/new", name="store_manager_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $storeManager = new StoreManager();
        $form = $this->createForm(StoreManagerType::class, $storeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($storeManager);
            $entityManager->flush();

            return $this->redirectToRoute('store_manager_index');
        }

        return $this->render('store_manager/new.html.twig', [
            'store_manager' => $storeManager,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="store_manager_show", methods={"GET"})
     */
    public function show(StoreManager $storeManager): Response
    {
        return $this->render('store_manager/show.html.twig', [
            'store_manager' => $storeManager,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="store_manager_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, StoreManager $storeManager): Response
    {
        $form = $this->createForm(StoreManagerType::class, $storeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('store_manager_index');
        }

        return $this->render('store_manager/edit.html.twig', [
            'store_manager' => $storeManager,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="store_manager_delete", methods={"DELETE"})
     */
    public function delete(Request $request, StoreManager $storeManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $storeManager->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($storeManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('store_manager_index');
    }
}
