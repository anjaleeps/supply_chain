<?php

namespace App\Controller;

use App\Entity\TrainSchedule;
use App\Form\TrainScheduleType;
use App\Repository\TrainScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/train/schedule")
 */
class TrainScheduleController extends AbstractController
{
    /**
     * @Route("/", name="train_schedule_index", methods={"GET"})
     */
    public function index(TrainScheduleRepository $trainScheduleRepository): Response
    {
        return $this->render('train_schedule/index.html.twig', [
            'train_schedules' => $trainScheduleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="train_schedule_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trainSchedule = new TrainSchedule();
        $form = $this->createForm(TrainScheduleType::class, $trainSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trainSchedule);
            $entityManager->flush();

            return $this->redirectToRoute('train_schedule_index');
        }

        return $this->render('train_schedule/new.html.twig', [
            'train_schedule' => $trainSchedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="train_schedule_show", methods={"GET"})
     */
    public function show(TrainSchedule $trainSchedule): Response
    {
        return $this->render('train_schedule/show.html.twig', [
            'train_schedule' => $trainSchedule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="train_schedule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TrainSchedule $trainSchedule): Response
    {
        $form = $this->createForm(TrainScheduleType::class, $trainSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('train_schedule_index');
        }

        return $this->render('train_schedule/edit.html.twig', [
            'train_schedule' => $trainSchedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="train_schedule_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TrainSchedule $trainSchedule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trainSchedule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trainSchedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('train_schedule_index');
    }
}
