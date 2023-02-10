<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonce", name="app_annonce")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $annonces = $doctrine->getRepository(Annonce::class)->findAll();
        return $this->render("annonce/index.html.twig", [
            "annonces" => $annonces
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="annonce_id", requirements={"id":"\d+"})
     */
    public function annonce($id, ManagerRegistry $doctrine)
    {
        $annonce = $doctrine->getRepository(Annonce::class)->find($id);
        return $this->render("annonce/produit.html.twig", ["annonce" => $annonce]);
    }

    /**
     * @Route("/annonce/add", name="annonce_add")
     */
    public function add(ManagerRegistry $doctrine, Request $requete)
    {
        $annonce = new Annonce();
        $annonce->setCreatedAt(new \DateTime());

        $formAnnonce = $this->createFormBuilder($annonce)
            ->add("title", TextType::class)
            ->add("prix", IntegerType::class)
            ->add("image", TextType::class)
            ->add("content", TextareaType::class)
            ->add("save", SubmitType::class)
            ->getForm();


        $formAnnonce->handleRequest($requete);
        #On vérifie si le bouton submit a été cliqué et si c'est valide
        if ($formAnnonce->isSubmitted() && $formAnnonce->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            #Créer un message flash
            $this->addFlash('add_success', "L'annonce a bien été ajouté !");

            return $this->redirectToRoute('annonce_show');
        }
        return $this->render("annonce/form-add-annonce.html.twig", [
            'formAnnonce' => $formAnnonce->createView()
        ]);
    }

    /**
     * @Route("/annonce/edit/{id}", name="annonce_edit")
     */
    public function edit($id, ManagerRegistry $doctrine, Request $requete)
    {
        $annonce = $doctrine->getRepository(Annonce::class)->find($id);
        // $formAnnonce = $this->createForm(AnnonceType::class, $annonce);
        $formAnnonce = $this->createFormBuilder($annonce)
            ->add("title", TextType::class)
            ->add("prix", IntegerType::class)
            ->add("image", TextType::class)
            ->add("content", TextareaType::class)
            ->add("save", SubmitType::class)
            ->getForm();

        $formAnnonce->handleRequest($requete);
        #On vérifie si le bouton submit a été cliqué et si c'est valide
        if ($formAnnonce->isSubmitted() && $formAnnonce->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            #Créer un message flash
            $this->addFlash('edit_success', "L'annonce a bien été modifié !");

            return $this->redirectToRoute('annonce_show');
        }
        return $this->render("annonce/form-edit-annonce.html.twig", [
            'formAnnonce' => $formAnnonce->createView()
        ]);

    }

    /**
     * @Route("/annonce/show", name="annonce_show")
     */
    public function show(ManagerRegistry $doctrine): Response
    {
        $annonces = $doctrine->getRepository(Annonce::class)->findAll();
        return $this->render("annonce/admin.html.twig", [
            "annonces" => $annonces
        ]);
    }

    /**
     * @Route("/annonce/delete/{id}", name="annonce_delete")
     */
    public function delete($id, ManagerRegistry $doctrine)
    {
        #Etape 1 : Récuperer l'objet qui a l'id : $id
        $annonce = $doctrine->getRepository(Annonce::class)->find($id);

        #Etape 2 : On appele l'entity manager de doctrine pour supprimer
        $em = $doctrine->getManager();
        $em->remove($annonce);
        $em->flush();

        #Créer un message flash
        $this->addFlash('delete_success', "L'annonce a bien été supprimé !");

        #Etape 3 : Rediriger vers une page ou afficher une page
        return $this->redirectToRoute('annonce_show');
    }
}
