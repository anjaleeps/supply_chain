<?php

namespace App\Controller;

use App\Entity\Route as RouteDef;
use App\Form\RouteType;
use App\Repository\RouteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/route")
 */
class RouteController extends AbstractController
{
    /**
     * @Route("/", name="route_index", methods={"GET"})
     */
    public function index(RouteRepository $routeRepository): Response
    {
        return $this->render('route/index.html.twig', [
            'routes' => $routeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="route_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $route = new RouteDef();
        $form = $this->createForm(RouteType::class, $route);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($route);
            $entityManager->flush();

            return $this->redirectToRoute('route_index');
        }

        return $this->render('route/new.html.twig', [
            'route' => $route,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="route_show", methods={"GET"})
     */
    public function show(RouteDef $route): Response
    {
        return $this->render('route/show.html.twig', [
            'route' => $route,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="route_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RouteDef $route): Response
    {
        $form = $this->createForm(RouteType::class, $route);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('route_index');
        }

        return $this->render('route/edit.html.twig', [
            'route' => $route,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="route_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RouteDef $route): Response
    {
        if ($this->isCsrfTokenValid('delete'.$route->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($route);
            $entityManager->flush();
        }

        return $this->redirectToRoute('route_index');
    }
}
