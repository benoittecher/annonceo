<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentaireType;
use App\Entity\Commentaire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index()
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }
    /**
     * @Route("/commentaire_add", name="commentaire_form", methods="GET")
     */
    public function form()
    {
        $form = $this->createForm(CommentaireType::class);
        return $this->render('commentaire/index.html.twig', [
            'form' => $form->createView()
        ]);

        
    }
    /**
     * @Route("/commentaire_add", name="commentaire_add", methods="POST")
     */
    public function add(Request $request, EntityManagerInterface $em, Session $session)
    {
            $commentaire = (new Commentaire)
                                        ->setMembreId($session->get("id"))
                                        ->setAnnonceId($request->request->get("commentaire")["annonce_id"])
                                        ->setCommentaire($request->request->get("commentaire")["commentaire"])
                                        ->setDateEnregistrement(date("Y-m-d H:i:s"));
            $em->persist($commentaire);
            $resultat = $em->flush();
            $message = "Le commentaire a bien été enregistré";
            $session->set("message", $message);
            return $this->redirectToRoute("home");
            
    }
}
