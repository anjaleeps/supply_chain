<?php

namespace App\Controller;

use App\Entity\TruckSchedule;
use App\Form\TruckScheduleType;
use App\Repository\TruckScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TruckScheduleController extends AbstractController
{
    /**
     * @Route("/truck/schedule", name="truck_schedule_index", methods={"GET"})
     */
    public function index(TruckScheduleRepository $truckScheduleRepository): Response
    {
        return $this->render('truck_schedule/index.html.twig', [
            'truck_schedules' => $truckScheduleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/store_manager/truck/schedule/new", name="truck_schedule_new", methods={"POST"})
     */
    public function new(Request $request, TruckScheduleRepository $truckScheduleRepository): Response
    {
       $route_id = $request->request->get('route_id');
       $driver_id = $request->request->get('driver_id');
       $assistant_id = $request->request->get('assistant_id');
       $truck_id = $request->request->get('truck_id');

       $truckScheduleRepository->scheduleTruckDelivery($route_id, $driver_id, $assistant_id, $truck_id);

       return new Response('success');
    }

    /**
     * @Route("/truck/schedule/{id}", name="truck_schedule_show", methods={"GET"})
     */
    public function show(TruckSchedule $truckSchedule): Response
    {
        return $this->render('truck_schedule/show.html.twig', [
            'truck_schedule' => $truckSchedule,
        ]);
    }

    /**
     * @Route("/truck/schedule/{id}/edit", name="truck_schedule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TruckSchedule $truckSchedule): Response
    {
        $form = $this->createForm(TruckScheduleType::class, $truckSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('truck_schedule_index');
        }

        return $this->render('truck_schedule/edit.html.twig', [
            'truck_schedule' => $truckSchedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/truck/schedule/{id}", name="truck_schedule_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TruckSchedule $truckSchedule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$truckSchedule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($truckSchedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('truck_schedule_index');
    }
}
