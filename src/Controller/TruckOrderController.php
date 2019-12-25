<?php

namespace App\Controller;

use App\Entity\TruckOrder;
use App\Form\TruckOrderType;
use App\Repository\TruckOrderRepository;
use App\Repository\TruckScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/truck/order")
 */
class TruckOrderController extends AbstractController
{
    /**
     * @Route("/", name="truck_order_index", methods={"GET"})
     */
    public function index(TruckOrderRepository $truckOrderRepository): Response
    {
        return $this->render('truck_order/index.html.twig', [
            'truck_orders' => $truckOrderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="truck_order_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $truckOrder = new TruckOrder();
        $form = $this->createForm(TruckOrderType::class, $truckOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($truckOrder);
            $entityManager->flush();

            return $this->redirectToRoute('truck_order_index');
        }

        return $this->render('truck_order/new.html.twig', [
            'truck_order' => $truckOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{orders}", name="truck_order_show", methods={"GET"})
     */
    public function show(TruckOrder $truckOrder): Response
    {
        return $this->render('truck_order/show.html.twig', [
            'truck_order' => $truckOrder,
        ]);
    }

    /**
     * @Route("/{orders}/edit", name="truck_order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TruckOrder $truckOrder): Response
    {
        $form = $this->createForm(TruckOrderType::class, $truckOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('truck_order_index');
        }

        return $this->render('truck_order/edit.html.twig', [
            'truck_order' => $truckOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{orders}", name="truck_order_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TruckOrder $truckOrder): Response
    {
        if ($this->isCsrfTokenValid('delete'.$truckOrder->getOrders(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($truckOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('truck_order_index');
    }

    /**
     * @Route("/driver/{truck_schedule_id}", name="orderList_show", methods={"GET"})
     */
    public function showOrdersToDriver( $truck_schedule_id,TruckOrderRepository $truckOrderRepository): Response
    {
        $truckOrders = $truckOrderRepository->findBy([
            'truck_schedule' => $truck_schedule_id,
        ]);

        return $this->render('driver/view_order_list.html.twig', [
            'truckOrders' => $truckOrders,
        ]);
    }
}
