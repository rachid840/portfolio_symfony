<?php

namespace App\Controller;

use App\Entity\Langages;
use App\Form\LangagesType;
use App\Repository\LangagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route("/langages")
 */
class LangagesController extends AbstractController
{
    /**
     * @Route("/", name="langages_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index() {
        $repository = $this->getDoctrine()->getRepository(Langages::class);
        $langagesList = $repository->findAll();

        return $this->render('langages/index.html.twig', [
            'langages' => $langagesList
        ]);
    }

    /**
     * @Route("/new", name="langages_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $langage = new Langages();
        $form = $this->createForm(LangagesType::class, $langage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($langage);
            $entityManager->flush();

            return $this->redirectToRoute('langages_index');
        }

        return $this->render('langages/new.html.twig', [
            'langage' => $langage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="langages_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Langages $langage): Response
    {
        return $this->render('langages/show.html.twig', [
            'langage' => $langage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="langages_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Langages $langage): Response
    {
        $form = $this->createForm(LangagesType::class, $langage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('langages_index');
        }

        return $this->render('langages/edit.html.twig', [
            'langage' => $langage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="langages_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Langages $langage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$langage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($langage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('langages_index');
    }
}
