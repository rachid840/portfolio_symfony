<?php

namespace App\Controller;

use App\Entity\Langages;
use App\Entity\Projets;
use App\Form\ContactType;
use App\Form\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="contact")
     */
    public function index(Request $request,\Swift_Mailer $mailer)
    {
        $form = $this->createForm(FormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // On crée le message
            $message = (new \Swift_Message('Nouveau contact'))

                // On attribue l'expéditeur
                ->setFrom($contact->getEmail())
                // On attribue le destinataire
                ->setTo('dusapingerard@gmail.com')
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);
            $this->addFlash('success', 'Votre message a bien été envoyé'); 
        }

        $repository = $this->getDoctrine()->getRepository(Projets::class);
        $projectsList = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(Langages::class);
        $langagesList = $repository->findAll();



        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
            'langages' => $langagesList,
            'projets' => $projectsList
            ]);
    }
}