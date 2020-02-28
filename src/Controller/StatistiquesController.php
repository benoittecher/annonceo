<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NoteRepository;
use App\Repository\AnnonceRepository;

class StatistiquesController extends AbstractController
{
    /**
     * @Route("/statistiques", name="statistiques")
     */
    public function index(NoteRepository $nr, AnnonceRepository $ar)
    {
        $top5membres = $nr->top5MembresNotes();
        $top5actifs = $ar->top5actifs();
        dd($top5actifs);
        $top5anciennes = $ar->top5anciennes();
        $top5cat = $ar->top5categories();
        return $this->render('statistiques/index.html.twig');
    }
    
}
