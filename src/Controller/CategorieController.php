<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategorieType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;

use Symfony\Component\HttpFoundation\Session\Session;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CategorieController.php',
        ]);
    }
    /**
     * @Route("/categorie_add", name="categorie_form", methods="GET")
     */
    public function form(Request $request, EntityManagerInterface $em)
    {
         
        $form = $this->createForm(CategorieType::class);
        return $this->render('categorie/index.html.twig', [
            'form' => $form->createView()
        ]);
    } 
    /**
     * @Route("/categorie_add", name="categorie_add", methods="POST")
     */
    public function add(Request $request, EntityManagerInterface $em, Session $session)
    {
        // Création du formulaire
        $form = $this->createForm(CategorieType::class);
        // Passage de la requête HTTP au formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // Récupération des données envoyées
            $data = $form->getData();
            $em->persist($data);
            $resultat = $em->flush();
            $this->addFlash('success', 'La catégorie a bien  été enregistrée !');
            $message = "La catégorie a bien été enregistrée";
            $session->set("message", $message);
        return $this->redirectToRoute("home");
        
            
        } elseif(!$form->isValid()){
            $this->addFlash('error', 'La catégorie n\'a pas été enregistrée !');
            $message = "Les données du formulaire ne sont pas valides";
            $session->set("message", $message);
            $this->createForm(CategorieType::class, $form->getData());
            return $this->render('categorie/index.html.twig', [
                'form' => $form->createView(),
            ]);

        }
    }
    
        
    
}
