<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository as CR;
use App\Repository\AnnonceRepository as AR;
use App\Repository\MembreRepository as MR;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;


class AccueilController extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function index(AR $annRepo, CR $catRepo, MR $membreRepo, Session $session)
    {
        $categories = $catRepo->findAll();
        $membres = $membreRepo->findAll();
        $regions = $annRepo->findRegions();
        $annonces = $annRepo->findAll();
        $message = $session->get("message");
        $session->set("message", "");
        return $this->render('base.html.twig', compact("categories", "membres", "annonces", "regions", "message"));
        
    }
    /**
    * @Route("/lister_villes", name="lister_villes")
    */
   public function villes_list(AR $repo)
   {
       $liste = array_unique($repo->findAll()->getVille());
       var_dump($liste);
       return $this->render("base.html.twig");
   }
   
}
