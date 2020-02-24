<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\AnnonceType;
use App\Form\MembreType;


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
            $em->persist($membre);
            $resultat = $em->flush();
            $message = "Le membre a bien été enregistré";
            $session->set("message", $message);
            return $this->redirectToRoute("home");
            
    }

    /**
     * @Route("/profil", name="profil")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profil(){
            return $this->render("membre/profil.html.twig");
        
            
        
    }

    /**
     * @Route("/profil/annonce/ajouter", name="nouvelle_annonce")
     */
    public function nouvelle_annonce(Request $rq, EntityManagerInterface $em){
        $form = $this->createForm(AnnonceType::class);
        $form->handleRequest($rq);

        $form = $form->createView();
        return $this->render("membre/annonce.html.twig", compact("form"));
            
    }

        
    
}
