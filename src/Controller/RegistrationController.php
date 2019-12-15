<?php

namespace App\Controller;

use App\Form\CustomerType;
use App\Form\ManagerType;
use App\Form\DriverAssistantType;
use App\Form\DriverType;
use App\Entity\Customer;
use App\Entity\Manager;
use App\Entity\Driver;
use App\Entity\DriverAssistant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register")
 */
class RegistrationController extends AbstractController
{

    /**
     * @Route("/", name="customer_registration")
     */
    public function registerCustomer(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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

            return $this->redirectToRoute('index');
        }
        return $this->render(
            'registration/registerCustomer.html.twig',
            array('form' => $form->createView())
        );
    }
    
    /**
     * @Route("/manager", name="manager_registration")
     */
    public function registerManager(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'registration/registerManager.html.twig',
            array('form' => $form->createView())
        );
    }
    
    /**
     * @Route("/driver", name="driver_registration")
     */

    public function registerDriver(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($driver, $driver->getPlainPassword());
            $driver->setPassword($password);
            $driver->setRoles(array('ROLE_DRIVER'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driver);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }
        return $this->render(
            'registration/registerDriver.html.twig',
            array('form' => $form->createView())
        );
    }
    

    /**
     * @Route("/driver_assistant", name="driver_assistant_registration")
     */
    public function registerDriverAssistant(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $driverAssistant = new DriverAssistant();
        $form = $this->createForm(DriverAssistantType::class, $driverAssistant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($driverAssistant, $driverAssistant->getPlainPassword());
            $driverAssistant->setPassword($password);
            $driverAssistant->setRoles(array('ROLE_DRIVER_ASSISTANT'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($driverAssistant);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }
        return $this->render(
            'registration/registerDriverAssistant.html.twig',
            array('form' => $form->createView())
        );
    }

}
