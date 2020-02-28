<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository as CR;
use App\Repository\AnnonceRepository as AR;
use App\Repository\MembreRepository as MR;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface as EMI;


class AccueilController extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function index(AR $annRepo, CR $catRepo, MR $membreRepo, Session $session, Request $request)
    {   
        $categories = $catRepo->findAll();
        $membres = $membreRepo->findByRole("ROLE_USER");
        $regions = $annRepo->findRegions();
        $cat_choisie = null;
        $prix_max_choisi = 0;
        $membre_choisi = null;
        $region_choisie = null;
        
        if($request->getMethod() == "POST"){
            $where = [];
            
                
                if($cat_choisie = $request->request->get("categorie")){
                    $where["categorie"] = $request->request->get("categorie");
                }
                if($membre_choisi = $request->request->get("membre")){
                    $where["membre"] = $request->request->get("membre");
                }
                if($region_choisie = $request->request->get("region")){
                    $where["ville"] = $request->request->get("region");
                }
                
               
                $annonces = $annRepo->findBy($where);

                if($prix_max_choisi = $request->request->get("prix")){
                    $annonces = array_filter($annonces, function($ann) use ($prix_max_choisi){
                        return $ann->getPrix() <= $prix_max_choisi;
                });
                
            
        
                }
        } else{ 
            $string = $request->query->get("recherche");
            if($string){
                $cats = $catRepo->recherche($string);
                $annonces= [];
                foreach($cats as $cat){
                    foreach ($cat->getAnnonces() as $annonceCategorie){
                        $annonces[] = $annonceCategorie;
                    }
                }
            }else{
                $annonces = $annRepo->findAll();
            }
        }
        
        $message = $annonces ? "" : "Nous sommes désolés, aucune annonce ne correspond à votre recherche.";
    
        return $this->render('accueil/index.html.twig', compact("prix_max_choisi", "cat_choisie", "region_choisie", "membre_choisi", "categories", "membres", "annonces", "regions", "message"));
        
        
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
   /**
     * @Route("/profil/filtrer", name="filtrer")
     */
    /*public function filtrer(AR $annRepo, CR $catRepo, MR $membreRepo, Request $request){
        if($form->isSubmitted()){
            $em = $this->container->get("doctrine.orm.default_entity_manager");
            $annonces_filtrees = $em->getRepository(Annonce::class)->findBy([
                "categorie" => $request->request->get("categorie")["id"],
                "membre" => $request->request->get("membre")["id"]
            ]);
            $categories = $catRepo->findAll();
            $membres = $membreRepo->findAll();
            $regions = $annRepo->findRegions();
            $annonces = $annRepo->findAll();
            return $this->render('accueil/index.html.twig', [
                "categories" => $categories,
                "membres" => $membres,
                "annonces" => $annonces_filtrees,
                "regions" => $regions,
                "message" => $message
            ]);
        }
    }
   */
}
