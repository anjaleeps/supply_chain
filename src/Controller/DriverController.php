<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Store;
use App\Entity\TruckSchedule;
use App\Repository\OrdersRepository;
use App\Repository\RouteRepository;
use App\Repository\TruckOrderRepository;
use App\Repository\TruckRepository;
use App\Security\DriverAuthenticator;
use App\Form\DriverType;
use App\Form\DriverButtonType;
use App\Repository\DriverRepository;
use App\Repository\TruckScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\Date;
use \DateTime;


/**
 * @Route("/")
 * 
 */
class DriverController extends AbstractController
{

    // /**
    //  * @Route("/", name="driver_index", methods={"GET"})
    //  *
    //  */
    // public function index(DriverRepository $driverRepository): Response
    // {
    //     return $this->render('driver/index.html.twig', [
    //         'drivers' => $driverRepository->findAll(),
    //     ]);

    // }

     /**
     * @Route("store_manager/driver/register", name="driver_registration")
     * 
     * @IsGranted("ROLE_STORE_MANAGER")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,  DriverAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($driver, $driver->getPlainPassword());
            $driver->setPassword($password);

            $driver->setRoles(array('ROLE_DRIVER'));
            $driver->setStatus('available');
            $driver->setWorkHours(new \DateTime('00:00:00'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driver);
            $entityManager->flush();

            return $this->redirectToRoute('driver_registration');

        }

        return $this->render(
            'registration/registerDriver.html.twig',
            array('form' => $form->createView())
        );
    }
    

    /**
     * @Route("driver/login", name="login_driver")
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

        return $this->render('security/driverLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("driver/logout", name="logout_driver")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    
    // /**
    //  * @Route("/new", name="driver_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $driver = new Driver();
    //     $form = $this->createForm(DriverType::class, $driver);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($driver);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('driver_index');
    //     }

    //     return $this->render('driver/new.html.twig', [
    //         'driver' => $driver,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("driver/{id}/edit", name="driver_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Driver $driver): Response
    {
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('driver_index');
        }

        return $this->render('driver/edit.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="driver_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Driver $driver): Response
    {
        if ($this->isCsrfTokenValid('delete' . $driver->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($driver);
            $entityManager->flush();
        }

        return $this->redirectToRoute('driver_index');
    }

    /**
     * @Route("driver/{id}/my-profile", name="driver_show", methods={"GET"})
     */
    public function show(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }




    /**
     * @Route("driver/{id}/driver_home", name="driver_home", methods={"GET"})
     */
    public function home( TruckScheduleRepository $truckScheduleRepository, TruckRepository $truckRepository, RouteRepository $routeRepository): Response
    {
        $driver=$this->getUser();
        $id=$driver->getId();
        $truckSchedule = $truckScheduleRepository->fetchUndeliveredSchedule($id);

        if ($truckSchedule!=null)
        {
            $truck_schedule_id= $truckSchedule[0]['id'];
            $truck_no=($truckRepository->fetchTruckNo($truckSchedule[0]['truck_id']))[0]['truck_no'];
            $route=($routeRepository->fetchRoute($truckSchedule[0]['route_id']))[0]['decription'];
            $status= $truckSchedule[0]['status'];

            return $this->render('driver/home.html.twig', [
                'truck_schedule_id'=> $truck_schedule_id,
                'driver' => $driver,
                'truck_no'=>$truck_no,
                'route'=>$route,
                'status'=>$status,
            ]);
        }
        else
        {
            return $this->render('driver/home.html.twig', [
                'driver' => $driver,
                'truck_no'=>'null',
            ]);
        }
    }


    /**
     * @Route("driver/{id}/{truck_schedule_id}/status", name="picked", methods={"POST"})
     */
    public function scheduleStatusPicked($id,$truck_schedule_id,TruckScheduleRepository $truckScheduleRepository, DriverRepository $driverRepository, Request $request)
    {
        $status = $request->request->get("status");
        if ($status=='Picked')
        {
            $truckScheduleRepository->setStatusPicked($truck_schedule_id);
        }
        elseif ($status=='Delivered')
        {
            $truckSchedule = $truckScheduleRepository->findOneBy([
                'driver' => $id,
                'status' => 'picked',
            ]);
            $driver_assistant_id=$truckSchedule->getDriverAssistant()->getId();
            $truck_id=$truckSchedule->getTruck()->getId();
            $truckScheduleRepository->setStatusDelivered($truck_schedule_id, $id,$driver_assistant_id,$truck_id);
        }

        return new JsonResponse( 'success');
    }

    /**
     * @Route("driver/{order_id}/orderdelivered", name="orderdelivered", methods={"POST"})
     */
    public function orderDelivered($order_id,OrdersRepository $ordersRepository, Request $request)
    {
        $ordersRepository->setStatusDelivered($order_id);

        return new Response( 'success');
    }

    /**
     * @Route("driver/{id}/{status}/change-status", name="change-status", methods={"POST"})
     */
    public function toggleAvailability($id,$status,DriverRepository $driverRepository, Request $request)
    {
        if ($status==1){
            $state="Available";
        }
        else{
            $state="Not available";
        }
        $driverRepository->changeAvailability($state,$id);
        return new Response( 'success');
    }

    /**
     * @Route("driver/{id}/show_orders", name="orderList_show", methods={"GET"})
     */
    public function showOrdersToDriver( $id, Driver $driver,TruckScheduleRepository $truckScheduleRepository,TruckOrderRepository $truckOrderRepository): Response
    {
        $truckSchedule = $truckScheduleRepository->fetchUndeliveredSchedule($id);

        if ($truckSchedule!=null) {

            $truck_schedule_id= $truckSchedule[0]['id'];

            $truckOrders = $truckOrderRepository->findBy([
                'truck_schedule' => $truck_schedule_id,
            ]);

            return $this->render('driver/view_order_list.html.twig', [
                'truckOrders' => $truckOrders,
                'driver' => $driver,
            ]);
        }  
        else
        {
            return $this->render('driver/home.html.twig', [
                'driver' => $driver,
                'truck_no'=>'null',
            ]);
        }
    }




}
