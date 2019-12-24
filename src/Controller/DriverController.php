<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Store;
use App\Entity\TruckSchedule;
use App\Security\DriverAuthenticator;
use App\Form\DriverType;
use App\Form\DriverButtonType;
use App\Repository\DriverRepository;
use App\Repository\TruckScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;


/**
 * @Route("/driver")
 * 
 */
class DriverController extends AbstractController
{

    /**
     * @Route("/", name="driver_index", methods={"GET"})
     *
     */
    public function index(DriverRepository $driverRepository): Response
    {
        return $this->render('driver/index.html.twig', [
            'drivers' => $driverRepository->findAll(),
        ]);

    }

     /**
     * @Route("/register", name="driver_registration")
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
            $driver->setStatus('idle');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driver);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $driver,
                $request,
                $authenticator,
                'driver_users'
            );
        }

        return $this->render(
            'registration/registerDriver.html.twig',
            array('form' => $form->createView())
        );
    }
    

    /**
     * @Route("/login", name="login_driver")
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
     * @Route("/logout", name="logout_driver")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    
    /**
     * @Route("/new", name="driver_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driver);
            $entityManager->flush();

            return $this->redirectToRoute('driver_index');
        }

        return $this->render('driver/new.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/driver_home", name="driver_home", methods={"GET"})
     */
    public function home($id, Driver $driver, TruckScheduleRepository $truckScheduleRepository): Response
    {

        $truckSchedule = $truckScheduleRepository->findOneBy([
            'driver' => $id,
//            'driver' => $driver->getId(),
            'status' => 'ready',
        ]);

        $truck_schedule_id=$truckSchedule->getId();
        $truck_no=$truckSchedule->getTruck()->getTruckNo();
        $route=$truckSchedule->getRoute()->getDecription();

//
//        $form = $this->createFormBuilder()
//            ->add('picked', SubmitType::class, ['label' => 'Picked up'])
//            ->add('delivered', SubmitType::class, ['label' => 'Delivered'])
//            ->getForm();
        $form = $this->createForm(DriverButtonType::class);

        if($form->get('picked')->isClicked()){
            $truckScheduleRepository->changeStatusPicked('1');
        }


        return $this->render('driver/home.html.twig', [
            'truck_schedule_id'=> $truck_schedule_id,
            'driver' => $driver,
            'truck_no'=>$truck_no,
            'route'=>$route,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{truck_schedule_id}/picked", name="picked", methods={"GET"})
     */
    public function scheduleStatusPicked($truck_schedule_id,TruckScheduleRepository $truckScheduleRepository): void
    {
//        $truckSchedule = $truckScheduleRepository->findOneBy([
//            'driver_id' => $id,
//            'status' => 'ready',
//        ]);

        $truckScheduleRepository->changeStatusPicked($truck_schedule_id);
    }

    /**
     * @Route("/{id}", name="orderlist_show", methods={"GET"})
     */
    public function showOders(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }

    /**
     * @Route("/{id}", name="driver_show", methods={"GET"})
     */
    public function show(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="driver_edit", methods={"GET","POST"})
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
}
