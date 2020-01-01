<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\DriverAssistant;
use App\Form\DriverAssistantType;
use App\Form\DriverButtonType;
use App\Repository\DriverAssistantRepository;
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
 * @Route("/")
 */
class DriverAssistantController extends AbstractController
{
    // /**
    //  * @Route("/", name="driver_assistant_index", methods={"GET"})
    //  */
    // public function index(DriverAssistantRepository $driverAssistantRepository): Response
    // {
    //     return $this->render('driver_assistant/index.html.twig', [
    //         'driver_assistants' => $driverAssistantRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("store_manager/driver_assistant/register", name="driver_assistant_registration")
     * 
     * @IsGranted("ROLE_STORE_MANAGER")
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

            return $this->redirectToRoute('driver_assistant_registration');

        }
        return $this->render(
            'registration/registerDriverAssistant.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("driver_assistant/login", name="login_driver_assistant")
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
     * @Route("driver_assistant/logout", name="logout_driver_assistant")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    // /**
    //  * @Route("/new", name="driver_assistant_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $driverAssistant = new DriverAssistant();
    //     $form = $this->createForm(DriverAssistantType::class, $driverAssistant);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($driverAssistant);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('driver_assistant_index');
    //     }

    //     return $this->render('driver_assistant/new.html.twig', [
    //         'driver_assistant' => $driverAssistant,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("driver_assistant/{id}/edit", name="driver_assistant_edit", methods={"GET","POST"})
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

    // /**
    //  * @Route("/{id}", name="driver_assistant_delete", methods={"DELETE"})
    //  */
    // public function delete(Request $request, DriverAssistant $driverAssistant): Response
    // {
    //     if ($this->isCsrfTokenValid('delete' . $driverAssistant->getId(), $request->request->get('_token'))) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->remove($driverAssistant);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('driver_assistant_index');
    // }

    /**
     * @Route("driver_assistant/{id}/my-profile", name="driver_assistant_show", methods={"GET"})
     */
    public function show(DriverAssistant $driverAssistant): Response
    {
        return $this->render('driver_assistant/show.html.twig', [
            'driver_assistant' => $driverAssistant,
        ]);
    }


    /**
     * @Route("driver_assistant/{id}/driver_assistant_home", name="driver_assistant_home", methods={"GET"})
     */
    public function home($id, DriverAssistant $driverAssistant, TruckScheduleRepository $truckScheduleRepository): Response
    {

        $truckSchedule = $truckScheduleRepository->findOneBy([
            'driver_assistant' => $id,
            'status' => 'ready',
        ]);

        if ($truckSchedule!=null)
        {
            $truck_schedule_id=$truckSchedule->getId();
            $truck_no=$truckSchedule->getTruck()->getTruckNo();
            $route=$truckSchedule->getRoute()->getDecription();

            return $this->render('driver_assistant/home.html.twig', [
                'truck_schedule_id'=> $truck_schedule_id,
                'driver_assistant' => $driverAssistant,
                'truck_no'=>$truck_no,
                'route'=>$route,
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
     * @Route("driver_assistant/{id}/{status}/assistant-change-status", name="assistant-change-status", methods={"POST"})
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




}
