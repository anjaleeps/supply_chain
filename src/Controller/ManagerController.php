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
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/manager")
 */
class ManagerController extends AbstractController
{
    // /**
    //  * @Route("/", name="manager_index", methods={"GET"})
    //  * 
    //  * @IsGranted("ROLE_MANAGER")
    //  */
    // public function index(ManagerRepository $managerRepository): Response
    // {
    //     return $this->render('manager/index.html.twig', [
    //         'managers' => $managerRepository->findAll(),
    //     ]);
    // }

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
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function getDashboard(TransportsRepository $transportsRepository): Response
    {
        $scheduledOrders = $transportsRepository->getScheduledOrders();
        
        $repository = $this->getDoctrine()->getRepository(Orders::class);
        $orders_placed = $repository->findBy(
            ['order_status' => 'placed']
        );

        return $this->render('manager/dashboard.html.twig', [
            'placed' => $orders_placed,
            'scheduled' => $scheduledOrders
        ]);
    }


    // /**
    //  * @Route("/new", name="manager_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $manager = new Manager();
    //     $form = $this->createForm(ManagerType::class, $manager);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($manager);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('manager_index');
    //     }

    //     return $this->render('manager/new.html.twig', [
    //         'manager' => $manager,
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/{id}", name="manager_show", methods={"GET"})
    //  */
    // public function show(Manager $manager): Response
    // {
    //     return $this->render('manager/show.html.twig', [
    //         'manager' => $manager,
    //     ]);
    // }

        // /**
        //  * @Route("/{id}/edit", name="manager_edit", methods={"GET","POST"})
        //  */
        // public function edit(Request $request, Manager $manager): Response
        // {
        //     $form = $this->createForm(ManagerType::class, $manager);
        //     $form->handleRequest($request);

        //     if ($form->isSubmitted() && $form->isValid()) {
        //         $this->getDoctrine()->getManager()->flush();

        //         return $this->redirectToRoute('manager_index');
        //     }

        //     return $this->render('manager/edit.html.twig', [
        //         'manager' => $manager,
        //         'form' => $form->createView(),
        //     ]);
        // }

    // /**
    //  * @Route("/{id}", name="manager_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, Manager $manager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $manager->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($manager);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('manager_index');
    // }

    /**
     * @Route("/report/products", name="products_report", methods={"GET"})
     * 
     * @IsGranted("ROLE_MANAGER")
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
     * 
     * @IsGranted("ROLE_MANAGER")
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
     * 
     * @IsGranted("ROLE_MANAGER")
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
     * 
     * @IsGranted("ROLE_MANAGER")
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
     * 
     * @IsGranted("ROLE_MANAGER")
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

    /**
     * @Route("/report/highest", name="highest_report", methods={"GET"})
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function generateHighestSaleData(ProductRepository $productRepository)
    {
        $highestSoldProducts = $productRepository->getHighestSoldProducts();
        $highestSoldCategories = $productRepository->getHighestSoldCategories();
        $data = [];

        for ($i = 0; $i < count($highestSoldProducts); $i++) {
            $data[$i]['year'] = $highestSoldProducts[$i]['year'];
            $data[$i]['month'] = $highestSoldProducts[$i]['month'];
            $data[$i]['product_name'] = $highestSoldProducts[$i]['product_name'];
            $data[$i]['product_sales_quantity'] = $highestSoldProducts[$i]['max_sales_quantity'];
            $data[$i]['category_name'] = $highestSoldCategories[$i]['category_name'];
            $data[$i]['category_sales_quantity'] = $highestSoldCategories[$i]['max_sales_quantity'];
        }

        return $this->render('report/highest.html.twig', [
            'highestSales' => $data
        ]);
    }

    /**
     * @Route("/report/quarter", name="quarterly_report", methods={"GET", "POST"})
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function generateQuarterlyReport($year = '2020', OrdersRepository $ordersRepository, Request $request)
    {
        if ($request->request->get('year')) {
            $year = $request->request->get('year');
        }
        $quarterReportData = $ordersRepository->getQuarterlyReport($year);
        $years = $ordersRepository->getRecordedYears();

        $data = [
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];

        foreach ($quarterReportData as $row) {
            $quarter = $row['quarter'];
            $data[$quarter] = $row;
        }


        return $this->render('report/quarter.html.twig', [
            'sales' => $quarterReportData,
            'years' => $years,
            'cur_year' => $year
        ]);
    }

    /**
     * @Route("/report/customer", name="customer_report", methods={"GET"})
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function generateCustomerReport(CustomerRepository $customerRepository)
    {
        $customerData = $customerRepository->getCustomerReport();
        $customers = [];

        foreach ($customerData as $customer) {
            if (!(array_key_exists($customer['customer_type'], $customers))) {
                $customers[$customer['customer_type']] = [];
            }
            array_push($customers[$customer['customer_type']], $customer);
        }

        return $this->render('report/customer.html.twig', [
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/report/work_dashboard", name="work_report", methods={"GET"})
     *
     * @IsGranted("ROLE_MANAGER")
     */

    public function getWorkDashboard(DriverRepository $driverRepository,DriverAssistantRepository $driverAssistantRepository,TruckRepository $truckRepository): Response
    {
        //driver
        $driverData = $driverRepository->getWorkedHours();
        $drivers = [];

        foreach ($driverData as $driver) {
            if (!(\array_key_exists($driver['city'], $drivers))) {
                $drivers[$driver['city']] = [];
            }
            array_push($drivers[$driver['city']], $driver);
        }

        //driverAssistant
        $driverAssistantData = $driverAssistantRepository->getWorkedHours();
        $driverAssistants = [];

        foreach ($driverAssistantData as $driverAssistant) {
            if (!(\array_key_exists($driverAssistant['city'], $driverAssistants))) {
                $driverAssistants[$driverAssistant['city']] = [];
            }
            array_push($driverAssistants[$driverAssistant['city']], $driverAssistant);
        }

        //trucks
        $truckData = $truckRepository->getWorkedHours();
        $trucks = [];

        foreach ($truckData as $truck) {
            if (!(\array_key_exists($truck['city'], $trucks))) {
                $trucks[$truck['city']] = [];
            }
            array_push($trucks[$truck['city']], $truck);
        }

        return $this->render('report/work_dashboard.html.twig', [
            'drivers' => $drivers,
            'driverAssistants'=>$driverAssistants,
            'trucks'=>$trucks,
        ]);
    }

    /**
     * @Route("/report/sales_dashboard", name="sales_dashboard", methods={"GET"})
     *
     * @IsGranted("ROLE_MANAGER")
     */
    public function getSalesDashboard(ProductRepository $productRepository,OrdersRepository $ordersRepository)
    {
        //product
        $productSales = $productRepository->getProductOrderCount();

        //sales
        $salesData = $ordersRepository->getSalesReport();
        $dataSales = [];

        foreach ($salesData as $sd) {

            if (!(\array_key_exists($sd['city'], $dataSales))) {
                $dataSales[$sd['city']] = [];
            }
            if (!(\array_key_exists($sd['route_id'], $dataSales[$sd['city']]))) {
                $dataSales[$sd['city']][$sd['route_id']] = [];
            }
            array_push($dataSales[$sd['city']][$sd['route_id']], $sd);
        }

        //highest sales
        $highestSoldProducts = $productRepository->getHighestSoldProducts();
        $highestSoldCategories = $productRepository->getHighestSoldCategories();
        $data = [];

        for ($i = 0; $i < count($highestSoldProducts); $i++) {
            $data[$i]['year'] = $highestSoldProducts[$i]['year'];
            $data[$i]['month'] = $highestSoldProducts[$i]['month'];
            $data[$i]['product_name'] = $highestSoldProducts[$i]['product_name'];
            $data[$i]['product_sales_quantity'] = $highestSoldProducts[$i]['max_sales_quantity'];
            $data[$i]['category_name'] = $highestSoldCategories[$i]['category_name'];
            $data[$i]['category_sales_quantity'] = $highestSoldCategories[$i]['max_sales_quantity'];
        }
        return $this->render('report/sales_dashboard.html.twig', [
            'products' => $productSales,
            'sales'=>$dataSales,
            'highestSales'=>$data,
        ]);
    }

    /**
     * @Route("/report/customer_dashboard", name="customer_dashboard", methods={"GET"})
     *
     * @IsGranted("ROLE_MANAGER")
     */
    public function getCustomerDashboard(CustomerRepository $customerRepository)
    {
        $customerData = $customerRepository->getCustomerReport();
        $customers = [];

        foreach ($customerData as $customer) {
            if (!(array_key_exists($customer['customer_type'], $customers))) {
                $customers[$customer['customer_type']] = [];
            }
            array_push($customers[$customer['customer_type']], $customer);
        }

        return $this->render('report/customer_dashboard.html.twig', [
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/report/quarter_dashboard", name="quarterly_dashboard", methods={"GET", "POST"})
     *
     * @IsGranted("ROLE_MANAGER")
     */
    public function getQuarterlyDashboard($year = '2020', OrdersRepository $ordersRepository, Request $request)
    {
        if ($request->request->get('year')) {
            $year = $request->request->get('year');
        }
        $quarterReportData = $ordersRepository->getQuarterlyReport($year);
        $years = $ordersRepository->getRecordedYears();

        $data = [
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];

        foreach ($quarterReportData as $row) {
            $quarter = $row['quarter'];
            $data[$quarter] = $row;
        }


        return $this->render('report/quarter_dashboard.html.twig', [
            'sales' => $quarterReportData,
            'years' => $years,
            'cur_year' => $year
        ]);
    }
}
