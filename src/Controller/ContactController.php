<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $contact = new Contact();
        $formContact = $this->createForm(ContactType::class, $contact);

        $formContact->handleRequest($request);
        #On vérifie si le bouton submit a été cliqué et si c'est valide
        if($formContact->isSubmitted() && $formContact->isValid())
        {
            #Etape 4bis : Appeler l'entityManager de doctrine pour l'enregistrement
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            #Créer un message flash
            $this->addFlash('add_success', "Le produit a bien été ajouté !");

            return $this->redirectToRoute('app_home');
        }
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
}
