<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MembreType;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;


class MembreController extends AbstractController
{
    /**
     * @Route("/membre", name="membre")
     */
    public function index()
    {
        return $this->render('membre/index.html.twig', [
            'controller_name' => 'MembreController',
        ]);
    }
    /**
     * @Route("/membre_add", name="membre_form", methods="get")
     */
    public function form()
    {
        $form = $this->createForm(MembreType::class);
        return $this->render('membre/index.html.twig', [
            'form' => $form->createView()
        ]);
        }
    /**
     * @Route("/membre_add", name="membre_add", methods="POST")
     */
    public function add(Request $request, EntityManagerInterface $em, Session $session)
    {
            $membre = (new Membre)
                                        ->setPseudo($request->request->get("membre")["pseudo"])
                                        ->setPassword($request->request->get("membre")["password"])
                                        ->setNom($request->request("membre")["nom"])
                                        ->setPrenom($request->request("membre")["prenom"])
                                        ->setTelephone($request->request("membre")["telephone"])
                                        ->setEmail($request->request("membre")["email"])
                                        ->setCivilite($request->request("membre")["civilite"])
                                        ->setRoles([$request->request("membre")["statut"]] ?? ["ROLE_USER"])
                                        ->setDateEnregistrement(date("Y-m-d H:i:s"));
            $em->persist($categorie);
            $resultat = $em->flush();
            $message = "La catégorie a bien été enregistrée";
            $session->set("message", $message);
            return $this->redirectToRoute("home");
            
    }

        
    
}
