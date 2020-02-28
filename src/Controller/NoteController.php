<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NoteType;
use App\Entity\Note;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MembreRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Form\AttribuerNoteType;

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="note")
     */
    public function index()
    {
        return $this->render('note/index.html.twig', [
            'controller_name' => 'NoteController',
        ]);
    }
    /**
     * @Route("/note_add", name="note_form", methods="GET")
     */
    public function form()
    {
        $form = $this->createForm(NoteType::class);
        return $this->render('note/index.html.twig', [
            'form' => $form->createView()
        ]);

        
    }
    /**
     * @Route("/note_add", name="note_add", methods="POST")
     */
    public function add(Request $request, EntityManagerInterface $em, Session $session, ManagerRegistry $registry)
    {
            $note = (new Note)
                                        ->setMembreNote($request->request->get("note")["membre_note"])
                                        ->setMembreNotant($session["membre_id"])
                                        ->setNote($request->request->get("note")["note"])
                                        ->setAvis($request->request->get("note")["avis"])
                                        ->setDateEnregistrement(date("Y-m-d H:i:s"));
                                        
                                        
            $em->persist($note);
            $resultat = $em->flush();
            $message = "La note a bien été enregistrée";
            $session->set("message", $message);
            return $this->redirectToRoute("home");
            
    }
    /**
     * @Route("/profil/attribuer-note/{pseudo}", name="attribuer_note")
     */
    public function attribuer(MembreRepository $mr, Request $rq, EntityManagerInterface $em, $pseudo){
        if($pseudo == $this->getUser()->getPseudo()){
            $this->addFlash("error", "Vous ne pouvez pas vous noter vous-même, petit salopiaud !");
            return $this->redirectToRoute("profil");
        }

        $membre = $mr->findOneBy([ "pseudo" => $pseudo ]);

        $form = $this->createForm(AttribuerNoteType::class);
        $form->handleRequest($rq);
        if($form->isSubmitted()){
            if($form->isValid()){
                $note = $form->getData();
                $note->setMembreNotant($this->getUser());
                $note->setDateEnregistrement(new \DateTime());
                $note->setMembreNote($membre);
                $em->persist($note);
                $em->flush();
                $this->addFlash("success", "Votre note a bien été prise en compte");
                return $this->redirectToRoute("profil");
            }
            else{
                $this->addFlash("error", "Une erreur est survenue !");
            }
        }

        $form = $form->createView();
        return $this->render("note/attribuer.html.twig", compact("membre", "form"));
    }
}
