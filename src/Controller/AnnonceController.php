<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;
use App\Repository\MembreRepository as MR;
use App\Repository\NoteRepository as NR;
use App\Repository\AnnonceRepository as AR;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Annonce;
use App\Repository\AnnonceRepository as RepAnn;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonce", name="annonce")
     */
    public function index()
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);
    }
    /**
     * @Route("/annonce_add", name="annonce_form", methods="GET")
     */
    public function form()
    {
        $form = $this->createForm(AnnonceType::class);
        return $this->render('annonce/index.html.twig', [ //à faire
            'form' => $form->createView()
        ]);

        
    }
    /**
     * @Route("/annonce_add", name="annonce_add", methods="POST")
     */
    public function add(Request $request, MR $membreRepo, AR $annRepo, EntityManagerInterface $em, Session $session)
    {
            $annonce = (new Annonce)
                                        ->setTitre($request->request->get("annonce")["titre"])
                                        ->setDescriptionCourte($request->request->get("annonce")["description_courte"])
                                        ->setDescriptionLongue($request->request->get("annonce")["description_longue"])
                                        ->setPrix($request->request->get("annonce")["prix"])
                                        ->setAdresse($request->request->get("annonce")["adresse"])
                                        ->setVille($request->request->get("annonce")["ville"])
                                        ->setCp($request->request->get("annonce")["cp"])
                                        ->setPays("France")
                                        ->setMembreId($session->get("id"))
                                        ->setPhoto($request->request->get("annonce")["photo"])
                                        ->setCategorieId($request->request->get("annonce")["categorie"])
                                        ->setDateEnregistrement(date("Y-m-d H:i:s"));
                                        
            $em->persist($annonce);
            $resultat = $em->flush();
            $message = "L'annonce a bien été enregistrée";
            $session->set("message", $message);
            return $this->redirectToRoute("home");
            
    }
    /**
     * @Route("/annonces", name="annonces")
     */
    public function list(RepAnn $ann){
        $annonces = $ann->findAll();
        return $this->render("annonce/list.html.twig", compact("annonces"));
    }
    /**
     * @Route("detailler_annonce/{annonce_id}", name="detailler_annonce")
     */
    public function detailler_annonce(AR $annRep, NR $noteRep, int $annonce_id){
        $annonce = $annRep->find($annonce_id);
        $moyenne = $noteRep->noteMoyenneRecue($annonce->getMembre()->getId());
        return $this->render("annonce/annonce_detaillee.html.twig", compact("annonce", 'moyenne'));
    }
    
}
