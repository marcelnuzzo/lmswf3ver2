<?php

namespace App\Controller;
//require '/vendor/autoload.php';

use App\Service;
use App\Entity\User;
use App\Entity\Answer;
use App\Form\LoadType;
use App\Form\QuizType;
use App\Form\Quiz2Type;
use App\Form\Quiz3Type;
use App\Form\Quiz4Type;
use App\Entity\Question;
use App\Entity\Testquiz;
use App\Service\Readfile;
use App\Service\Writefile;
use App\Repository\QcmRepository;
use App\Repository\UserRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Symfony\Bridge\Twig\Node\RenderBlockNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/userQuiz", name="home_userQuiz")
     */
    public function userQuiz(Request $request, EntityManagerInterface $manager, AnswerRepository $repo, QuestionRepository $questionRepo, UserRepository $userRepo)
    {
        
        $question = $questionRepo->findFirstId()[0]['id'];
        $id = $question;  
        $tabPropo = [];
        for($i=0; $i<3; $i++) {
            $answers = $repo->findPropo($id)[$i];
            $answers = $answers['proposition'];
            $tabPropo[] = $answers;
        }
        dd($request);
        $answer = new Answer();
        $form = $this->createForm(Quiz4Type::class, $answer, [
            'question' => $question,
            'tabPropo' => $tabPropo,
        ]);
        $form->handleRequest($request);
        $count = 0;
        $ok = "";
        $user = $this->getUser();
        if($user != null) {
            $user = $this->getUser()->getId();   
            $user = $userRepo->find($user);    
            if($user->getOkquiz() != 0)
                $ok = true;
            else
                $ok = false;
        }
        
        if($form->isSubmitted() && $form->isValid()) {
            
            $correction = $repo->findByCorrection($question);  
            $correction = $correction[0]->getId();   
            $idProposition = $answer->getProposition();
            if($correction == $idProposition){
                if($user != null) {
                    $user->setOkquiz(true);
                    $manager->persist($user);
                    $manager->flush();
                    $count++;
                }  
                $this->addFlash(
                    'success',
                    "Bonne réponse"
                );
                return $this->redirectToRoute('home_userQuiz');
            } else {
                $this->addFlash(
                    'danger',
                    "Mauvaise réponse !"
                );
            }
            
        }

        return $this->render('home/userQuiz2.html.twig', [
            'form' => $form->createView(),
            'count' => $count,
            'user' => $user,
            'ok' => $ok,
            'questions' => $questionRepo->findAll(),
        ]);
    }

    /**
     * @Route("/home/raz", name="home_raz")
     */
    public function raz(UserRepository $userRepo, EntityManagerInterface $manager)
    {
        $user = $this->getUser()->getId();
        $user = $userRepo->find($user);
        $user->setOkquiz(false);
        $manager->persist($user);
        $manager->flush();
        
        //dd($user);
        return $this->redirectToRoute('homepage');
        
    }
}
