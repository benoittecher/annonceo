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
}
