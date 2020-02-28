<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    /**
     * @Route("/membre/top5notes", name="top5notes")
     */
    public function top5notes(){
        $requete = $this->createQueryBuilder('n')
                        ->select("AVG(n.note)")
                        ->join("n.membre_note", "m")
                        ->where("m.id = :id")
                        ->groupBy("m.id")
                        ->setParameter("id", $id_membre)
                        ->getQuery()
                        ->getResult();
    }
}
