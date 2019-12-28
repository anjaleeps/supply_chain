<?php

namespace App\Controller;

use App\Entity\TrainSchedule;
use App\Form\TrainScheduleType;
use App\Repository\TrainScheduleRepository;
use App\Repository\TransportsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/")
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
     * @Route("/manager/train/schedule/new", name="train_schedule_new", methods={"POST"})
     * 
     * @IsGranted("ROLE_MANAGER")
     */
    public function scheduleTrainTransport(Request $request, TransportsRepository $transportsRepository): Response
    {
        $order_id = $request->request->get("order_id");
        // $date = $request->request->get("date");

        $trainData = $transportsRepository->scheduleTrainTransport($order_id);
        return new JsonResponse($trainData[0]);    
    }

    
    /**
     * @Route("/store_manager/train/schedule/edit", name="train_schedule_edit", methods={"POST"})
     * 
     * @IsGranted("ROLE_STORE_MANAGER")
     */
    public function updateTrainStatus(Request $request, TransportsRepository $transportsRepository){
        $train_id = $request->request->get('train_id');
        $user_id = $this->getUser()->getId();
        
        $rows = $transportsRepository->updateTrainStatus($train_id, $user_id);
        
        return new Response('success');
      

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

    // /**
    //  * @Route("/{id}/edit", name="train_schedule_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, TrainSchedule $trainSchedule): Response
    // {
    //     $form = $this->createForm(TrainScheduleType::class, $trainSchedule);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('train_schedule_index');
    //     }

    //     return $this->render('train_schedule/edit.html.twig', [
    //         'train_schedule' => $trainSchedule,
    //         'form' => $form->createView(),
    //     ]);
    // }

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
