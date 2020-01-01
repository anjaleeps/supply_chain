<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\DriverAssistant;
use App\Form\DriverAssistantType;
use App\Form\DriverButtonType;
use App\Repository\DriverAssistantRepository;
use App\Repository\RouteRepository;
use App\Repository\TruckOrderRepository;
use App\Repository\TruckRepository;
use App\Repository\TruckScheduleRepository;
use App\Security\DriverAssistantAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * @Route("/driver_assistant")
 */
class DriverAssistantController extends AbstractController
{
    /**
     * @Route("/", name="driver_assistant_index", methods={"GET"})
     */
    public function index(DriverAssistantRepository $driverAssistantRepository): Response
    {
        return $this->render('driver_assistant/index.html.twig', [
            'driver_assistants' => $driverAssistantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/register", name="driver_assistant_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,  DriverAssistantAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $driverAssistant = new DriverAssistant();
        $form = $this->createForm(DriverAssistantType::class, $driverAssistant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($driverAssistant, $driverAssistant->getPlainPassword());
            $driverAssistant->setPassword($password);
            
            $driverAssistant->setRoles(array('ROLE_DRIVER_ASSISTANT'));
            $driverAssistant->setStatus('available');
            $driverAssistant->setWorkHours(new \DateTime('00:00:00'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driverAssistant);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                    $driverAssistant,
                    $request,
                    $authenticator,
                    'driver_assistant_users'
                );
        }
        return $this->render(
            'registration/registerDriverAssistant.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/login", name="login_driver_assistant")
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

        return $this->render('security/driverAssistantLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout_driver_assistant")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/new", name="driver_assistant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $driverAssistant = new DriverAssistant();
        $form = $this->createForm(DriverAssistantType::class, $driverAssistant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driverAssistant);
            $entityManager->flush();

            return $this->redirectToRoute('driver_assistant_index');
        }

        return $this->render('driver_assistant/new.html.twig', [
            'driver_assistant' => $driverAssistant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="driver_assistant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DriverAssistant $driverAssistant): Response
    {
        $form = $this->createForm(DriverAssistantType::class, $driverAssistant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('driver_assistant_index');
        }

        return $this->render('driver_assistant/edit.html.twig', [
            'driver_assistant' => $driverAssistant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="driver_assistant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DriverAssistant $driverAssistant): Response
    {
        if ($this->isCsrfTokenValid('delete' . $driverAssistant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($driverAssistant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('driver_assistant_index');
    }

    /**
     * @Route("/{id}/my-profile", name="driver_assistant_show", methods={"GET"})
     */
    public function show(DriverAssistant $driverAssistant): Response
    {
        return $this->render('driver_assistant/show.html.twig', [
            'driver_assistant' => $driverAssistant,
        ]);
    }


    /**
     * @Route("/driver_assistant_home", name="driver_assistant_home", methods={"GET"})
     */
    public function home( TruckScheduleRepository $truckScheduleRepository, TruckRepository $truckRepository, RouteRepository $routeRepository): Response
    {
        $driverAssistant=$this->getUser();
        $id=$driverAssistant->getId();
        $truckSchedule = $truckScheduleRepository->fetchUndeliveredScheduleDriverAssistant($id);

        if ($truckSchedule!=null)
        {
            $truck_schedule_id= $truckSchedule[0]['id'];
            $truck_no=($truckRepository->fetchTruckNo($truckSchedule[0]['truck_id']))[0]['truck_no'];
            $route=($routeRepository->fetchRoute($truckSchedule[0]['route_id']))[0]['decription'];
            $status= $truckSchedule[0]['status'];

            return $this->render('driver_assistant/home.html.twig', [
                'truck_schedule_id'=> $truck_schedule_id,
                'driver_assistant' => $driverAssistant,
                'truck_no'=>$truck_no,
                'route'=>$route,
                'status'=>$status,
            ]);
        }
        else
        {
            return $this->render('driver_assistant/home.html.twig', [
                'driver_assistant' => $driverAssistant,
                'truck_no'=>'null',
            ]);
        }
    }
    /**
     * @Route("/{id}/{status}/assistant-change-status", name="assistant-change-status", methods={"POST"})
     */
    public function toggleAvailability($id,$status,DriverAssistantRepository $driverAssistantRepository, Request $request)
    {
        if ($status==1){
            $state="Available";
        }
        else{
            $state="Not available";
        }
        $driverAssistantRepository->changeAvailability($state,$id);
        return new Response( 'success');
    }

    /**
     * @Route("/{id}/show_orders", name="orderList_show_", methods={"GET"})
     */
    public function showOrdersToDriver( $id, DriverAssistant $driverAssistant,TruckScheduleRepository $truckScheduleRepository,TruckOrderRepository $truckOrderRepository): Response
    {
        $truckSchedule = $truckScheduleRepository->fetchUndeliveredScheduleDriverAssistant($id);

        if ($truckSchedule!=null) {

            $truck_schedule_id= $truckSchedule[0]['id'];

            $truckOrders = $truckOrderRepository->findBy([
                'truck_schedule' => $truck_schedule_id,
            ]);

            return $this->render('driver_assistant/view_order_list.html.twig', [
                'truckOrders' => $truckOrders,
                'driver_assistant' => $driverAssistant,
            ]);
        }
        else
        {
            return $this->render('driver_assistant/home.html.twig', [
                'driver_assistant' => $driverAssistant,
                'truck_no'=>'null',
            ]);
        }
    }



}
