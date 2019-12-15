<?php

namespace App\Controller;

use App\Entity\DriverAssistant;
use App\Form\DriverAssistantType;
use App\Repository\DriverAssistantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/driver/assistant")
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
     * @Route("/{id}", name="driver_assistant_show", methods={"GET"})
     */
    public function show(DriverAssistant $driverAssistant): Response
    {
        return $this->render('driver_assistant/show.html.twig', [
            'driver_assistant' => $driverAssistant,
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
        if ($this->isCsrfTokenValid('delete'.$driverAssistant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($driverAssistant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('driver_assistant_index');
    }
}
