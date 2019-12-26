<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\Orders;
use App\Entity\Transports;
use App\Form\ManagerType;
use App\Repository\ManagerRepository;
use App\Repository\TransportsRepository;
use App\Security\ManagerAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Repository\ProductRepository;
use App\Repository\DriverRepository;
use App\Repository\DriverAssistantRepository;
use App\Repository\OrdersRepository;
use App\Repository\TruckRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/manager")
 */
class ManagerController extends AbstractController
{
    /**
     * @Route("/", name="manager_index", methods={"GET"})
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function index(ManagerRepository $managerRepository): Response
    {
        return $this->render('manager/index.html.twig', [
            'managers' => $managerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/register", name="manager_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ManagerAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $manager = new Manager();
        $form = $this->createForm(ManagerType::class, $manager);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($manager, $manager->getPlainPassword());
            $manager->setPassword($password);
            $manager->setRoles(array('ROLE_MANAGER'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($manager);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $manager,
                $request,
                $authenticator,
                'manager_users'
            );
        }

        return $this->render(
            'registration/registerManager.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/login", name="login_manager")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/managerLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout_manager")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/dashboard", name="manager_dashboard", methods={"GET"})
     */
    public function getDashboard(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Orders::class);
        $orders_placed = $repository->findBy(
            ['order_status' => 'Placed']
        );
        $orders_on_sore = $repository->findBy(
            ['order_status' => 'On Store']
        );


        return $this->render('manager/dashboard.html.twig', [
            'placed' => $orders_placed,
            'on_sore' => $orders_on_sore,
        ]);
    }
    /**
     * @Route("/dashboard/status", name="manager_change_transport",methods={"POST"})
     */
    public function changeTransport(TransportsRepository $transportsRepository, Request $request)
    {
        $order_id = $request->request->get("order_id");
        $date = $request->request->get("date");

        //$date = \DateTime::createFromFormat('Y-m-d', $date);
        //dd($date);
        $transportsRepository->scheduleTrainTransport($order_id, $date);
        return new Response( 'success');
    }

    /**
     * @Route("/new", name="manager_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $manager = new Manager();
        $form = $this->createForm(ManagerType::class, $manager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($manager);
            $entityManager->flush();

            return $this->redirectToRoute('manager_index');
        }

        return $this->render('manager/new.html.twig', [
            'manager' => $manager,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manager_show", methods={"GET"})
     */
    public function show(Manager $manager): Response
    {
        return $this->render('manager/show.html.twig', [
            'manager' => $manager,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="manager_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Manager $manager): Response
    {
        $form = $this->createForm(ManagerType::class, $manager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('manager_index');
        }

        return $this->render('manager/edit.html.twig', [
            'manager' => $manager,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="manager_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Manager $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $manager->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($manager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('manager_index');
    }

    /**
     * @Route("/report/products", name="products_report", methods={"GET"})
     */
    public function generateProductReport(ProductRepository $productRepository)
    {
        $productSales = $productRepository->getProductOrderCount();
        return $this->render('report/product.html.twig', [
            'products' => $productSales,
        ]);
    }


    /**
     * @Route("/report/drivers", name="drivers_report", methods={"GET"})
     */
    public function generateDriverReport(DriverRepository $driverRepository)
    {
        $driverData = $driverRepository->getWorkedHours();
        $drivers = [];

        foreach ($driverData as $driver) {
            if (!(\array_key_exists($driver['city'], $drivers))) {
                $drivers[$driver['city']] = [];
            }
            array_push($drivers[$driver['city']], $driver);
        }

        return $this->render('report/driver.html.twig', [
            'drivers' => $drivers,
        ]);
    }

    /**
     * @Route("/report/driver_assistants", name="driver_assistants_report", methods={"GET"})
     */
    public function generateDriverAssistantReport(DriverAssistantRepository $driverAssistantRepository)
    {
        $driverAssistantData = $driverAssistantRepository->getWorkedHours();
        $driverAssistants = [];

        foreach ($driverAssistantData as $driverAssistant) {
            if (!(\array_key_exists($driverAssistant['city'], $driverAssistants))) {
                $driverAssistants[$driverAssistant['city']] = [];
            }
            array_push($driverAssistants[$driverAssistant['city']], $driverAssistant);
        }


        return $this->render('report/driver_assistant.html.twig', [
            'driverAssistants' => $driverAssistants,
        ]);
    }

    /**
     * @Route("/report/trucks", name="trucks_report", methods={"GET"})
     */
    public function generateTruckReport(TruckRepository $truckRepository)
    {
        $truckData = $truckRepository->getWorkedHours();
        $trucks = [];

        foreach ($truckData as $truck) {
            if (!(\array_key_exists($truck['city'], $trucks))) {
                $trucks[$truck['city']] = [];
            }
            array_push($trucks[$truck['city']], $truck);
        }

        return $this->render('report/truck.html.twig', [
            'trucks' => $trucks,
        ]);
    }


    /**
     * @Route("/report/sales", name="sales_report", methods={"GET"})
     */
    public function generateSalesReport(OrdersRepository $ordersRepository)
    {
        $salesData = $ordersRepository->getSalesReport();
        $data = [];

        foreach ($salesData as $sd) {

            if (!(\array_key_exists($sd['city'], $data))) {
                $data[$sd['city']] = [];
            }
            if (!(\array_key_exists($sd['route_id'], $data[$sd['city']]))) {
                $data[$sd['city']][$sd['route_id']] = [];
            }
            array_push($data[$sd['city']][$sd['route_id']], $sd);
        }

        return $this->render('report/sales.html.twig', [
            'sales' => $data,
        ]);
    }
}
