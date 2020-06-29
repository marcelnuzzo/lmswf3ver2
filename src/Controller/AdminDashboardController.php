<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Service\StatsService;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminDashboardController extends AbstractController
{

    /**
     * Permet de visualiser toutes les questions et les réponses avec leurs corrections
     * 
     * @Route("/admin", name="admin_dashboard")
     */
    public function index()
    {      
        $answers = $this->getDoctrine()
        ->getRepository(Answer::class)
        ->findAll();
        //->findBy(['correction' => 'vrai"']);
        return $this->render('admin/dashboard/index.html.twig', [
            'answers' => $answers
        ]);
    }
}
