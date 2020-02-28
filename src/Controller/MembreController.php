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
use App\Entity\Photo, Datetime;
use App\Repository\MembreRepository as MR;
use App\Repository\AnnonceRepository as AR;
use App\Repository\PhotoRepository as PR;
use App\Repository\NoteRepository as NR;


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
    public function profil(NR $rep){
        $membre = $this->getUser();
        $moyenne = $rep->noteMoyenneRecue($membre->getId());
        $mes_annonces = $membre->getAnnonces();
        return $this->render("membre/profil.html.twig", ["mes_annonces" => $mes_annonces, "moyenne" => $moyenne]);
        
        
            
        
    }

    /**
     * @Route("/profil/annonce/ajouter", name="nouvelle_annonce")
     */
    public function nouvelle_annonce(Request $rq, EntityManagerInterface $em){
        $form = $this->createForm(AnnonceType::class);
        $form->handleRequest($rq);

        if($form->isSubmitted()){
            if($form->isValid()){
                $nvlAnnonce = $form->getData();
                $album = new Photo;
                $destination = $this->getParameter("dossier_images_annonces");
                for($i=1; $i<=5; $i++){
                    $champ = "photo" . $i;
                    if($photoUploadee = $form[$champ]->getData()){
                    $nomPhoto = pathinfo($photoUploadee->getClientOriginalName(), PATHINFO_FILENAME);
                    $nouveauNom = trim($nomPhoto);
                    $nouveauNom = str_replace(" ", "_", $nouveauNom);
                    $nouveauNom .= "-" . uniqid() . "." . $photoUploadee->guessExtension();
                    $photoUploadee->move($destination, $nouveauNom); 
                    $setter = "setPhoto$i";
                    $album->$setter($nouveauNom);
                    }
                }
                $em->persist($album);
                $em->flush();
                $nvlAnnonce->setDateEnregistrement(new DateTime());
                $nvlAnnonce->setPhoto($album);
                $nvlAnnonce->setMembre($this->getUser());
                $em->persist($nvlAnnonce);
                $em->flush();
                $this->addFlash("success", "Votre annonce a bien été enregistrée");
                return $this->redirectToRoute("profil");

            }
            else{
                $this->addFlash("error", "Votre annonce n'a pas été enregistrée");
            }
        }

        $form = $form->createView();
        return $this->render("membre/annonce.html.twig", compact("form"));
            
    }
    /**
     * @Route("/profil/modifier_annonce/{annonce_id}", name="modifier_annonce")
     */
    public function modifier_annonce(AR $annRep, EntityManagerInterface $em, Request $request, int $annonce_id){
    
        
        $annonceOrigine = $annRep->find($annonce_id);
        if($this->getUser()->getId() != $annonceOrigine->getMembre()->getId()){
            $this->addFlash("error", "Vous n'avez pas le droit de modifier l'annonce d'un autre utilisateur.");
            return $this->redirectToRoute("home");
        }
        $form = $this->createForm(AnnonceType::class, $annonceOrigine);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()){
            if($form->isValid()){
                $destination = $this->getParameter("dossier_images_annonces");
                for($i=1; $i<=5; $i++){
                    $champ = "photo" . $i;
                    if($photoUploadee = $form[$champ]->getData()){
                    $nomPhoto = pathinfo($photoUploadee->getClientOriginalName(), PATHINFO_FILENAME);
                    $nouveauNom = trim($nomPhoto);
                    $nouveauNom = str_replace(" ", "_", $nouveauNom);
                    $nouveauNom .= "-" . uniqid() . "." . $photoUploadee->guessExtension();
                    $photoUploadee->move($destination, $nouveauNom); 
                    $setter = "setPhoto$i";
                    $annonceOrigine->getPhoto->$setter($nouveauNom);
                    }
                }
                /*$em->persist($album);
                $em->flush();*/
                $em->persist($annonceOrigine);
                $em->flush();
                $this->addFlash("success", "Votre annonce a bien été modifiée");
                return $this->redirectToRoute("profil");

            }
            else{
                $this->addFlash("error", "Votre annonce n'a pas été modifiée");
            }
        }
        return $this->render("annonce/modification.html.twig", [
            "form" => $form->createView()
        ]);


    }
    

    



        
    
}
