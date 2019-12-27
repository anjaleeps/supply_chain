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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\Date;
use \DateTime;


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
            $driver->setWorkHours(new \DateTime('00:00:00'));

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




    /**
     * @Route("/{id}/driver_home", name="driver_home", methods={"GET"})
     */
    public function home($id, Driver $driver, TruckScheduleRepository $truckScheduleRepository): Response
    {

        $truckSchedule = $truckScheduleRepository->findOneBy([
            'driver' => $id,
            'status' => 'ready',
        ]);
        if ($truckSchedule!=null)
        {
            $truck_schedule_id=$truckSchedule->getId();
            $truck_no=$truckSchedule->getTruck()->getTruckNo();
            $route=$truckSchedule->getRoute()->getDecription();

            return $this->render('driver/home.html.twig', [
                'truck_schedule_id'=> $truck_schedule_id,
                'driver' => $driver,
                'truck_no'=>$truck_no,
                'route'=>$route,
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
     * @Route("/{id}/{truck_schedule_id}/picked", name="picked", methods={"POST"})
     */
    public function scheduleStatusPicked($id,Driver $driver,$truck_schedule_id,TruckScheduleRepository $truckScheduleRepository, DriverRepository $driverRepository, Request $request)
    {

        $status = $request->request->get("status");
        if ($status=='Picked')
        {
            $truckScheduleRepository->setStatusPicked($truck_schedule_id);
            $start_time = new DateTime();
            $driverRepository->updateWorkHours($id, $start_time->format('Y-m-d H:i:s'));

        }
        elseif ($status=='Delivered')
        {
            $truckScheduleRepository->setStatusDelivered($truck_schedule_id);
            $driverRepository->calculateWorkHours($id);

        }

        return new Response( 'success');
    }



    /**
     * @Route("/{id}/my-profile", name="driver_show", methods={"GET"})
     */
    public function show(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }
    public function getTimeDiff($dtime,$atime)
    {
        $nextDay = $dtime>$atime?1:0;
        $dep = explode(':',$dtime);
        $arr = explode(':',$atime);
        $diff = abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));
        $hours = floor($diff/(60*60));
        $mins = floor(($diff-($hours*60*60))/(60));
        $secs = floor(($diff-(($hours*60*60)+($mins*60))));
        if(strlen($hours)<2){$hours="0".$hours;}
        if(strlen($mins)<2){$mins="0".$mins;}
        if(strlen($secs)<2){$secs="0".$secs;}
        return $hours.':'.$mins.':'.$secs;
    }

    /**
     * @Route("/{id}/updateWorkHours", name="driver_show", methods={"GET"})
     */
//    public function updateWorkHours(Driver $driver): Response
//    {
//        $stopwatch = new Stopwatch();
//// starts event named 'eventName'
//        $stopwatch->start('eventName');
//// ... some code goes here
//        $event = $stopwatch->stop('eventName');
//        ]);
//    }

}
