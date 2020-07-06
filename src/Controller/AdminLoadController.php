<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\Answer;
use App\Form\LoadType;
use App\Form\Quiz4Type;
use App\Entity\Question;
use App\Service\Loadcsv;
use App\Form\LoadCsvType;
use App\Service\Readfile;
use App\Repository\UserRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminLoadController extends AbstractController
{
    /**
     * @Route("/admin/load", name="admin_load")
     */
    /*
    public function index()
    {
        return $this->render('admin/load/index.html.twig', [
            'controller_name' => 'AdminLoadController',
        ]);
    }
    */

    /**
     * @Route("/admin/load/loadxls", name="admin_load_loadxls")
     */
    public function loadxls(Request $request, EntityManagerInterface $manager, Readfile $readfile) 
    {
        $form = $this->createForm(LoadType::class);
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()) {
            $donnee = $form->getData();
            $fichier = $donnee['Chargement'];
            $getHighestRow1 = $readfile->getRead2($fichier)[0];
            $getHighestRow2 = $readfile->getRead2($fichier)[2];
            $valCell1 = $readfile->getRead2($fichier)[1];
            $valCell2 = $readfile->getRead2($fichier)[3];
            //$sheet = $readfile->getRead2($fichier);
            
                $ctr=0;
                for($i=1; $i<=$getHighestRow2; $i++) {
                    $question = new Question();
                    $question->setLabel($valCell2[$ctr]);
                    $ctr++;               
                    $manager->persist($question);
                }
                $manager->flush();

                $questionRepo = $manager->getRepository('App:Question');
                $ctr=0;
                $ctr2=1;
                for($i=1; $i<=$getHighestRow1; $i++) {
                    $answer = new Answer();
                    $answer->setQuestions($questionRepo->find($ctr2));
                    $ctr++;
                    $answer->setProposition($valCell1[$ctr]);
                    $ctr++;
                    $answer->setCorrection($valCell1[$ctr]);
                    $ctr++;                
                    $manager->persist($answer);
                    if($i == 3) {
                        $ctr2++;
                    }
                }
                $manager->flush();
                return $this->redirectToRoute('admin_index');
                
            }
        
        return $this->render('admin/load/loadxls.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/admin/load/loadcsv", name="admin_load_loadcsv")
     */
    public function loadcsv(EntityManagerInterface $manager, QuestionRepository $repo, Request $request, Loadcsv $loadcsv)
    {
        $form = $this->createForm(LoadCsvType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
 
            $donnee = $form->getData();
            $fichier = $donnee['Chargement'];
            $filename = substr(strrchr($fichier, "."), 1);
            if($filename == "csv") {
                
            $nbQuestion = $loadcsv->getRead3($fichier)[0];
            $tabQuestion = $loadcsv->getRead3($fichier)[1];
            $choice = $loadcsv->getRead3($fichier)[2];
            $nbProposition = $loadcsv->getRead3($fichier)[3];
            $tab2 = $loadcsv->getRead3($fichier)[4];
            $tabProposition = $loadcsv->getRead3($fichier)[5];
            $tabCorrection = $loadcsv->getRead3($fichier)[6];
            $titre = $loadcsv->getRead3($fichier)[7];

            $quizz = new Quizz();
            $quizz->setTitre($titre);
            $manager->persist($quizz);
      
            for($i=0; $i<$nbQuestion; $i++) {
                $question = new Question();
                $question->setLabel($tabQuestion[$i])
                        ->setChoice($choice[$i])
                        ->setQuizz($quizz);

                $manager->persist($question);
                
            }
            $manager->flush();

            $ctr = 0;
            $repo = $manager->getRepository('App:Question');
            $firstQuestion = $repo->findFirstId()[0]['id'];
            $id = $firstQuestion;
            
            for($j=1; $j<=$nbProposition; $j++) {   
                if($j < $tab2[0]) {
                    $id = $id;
                }
                for($k=0; $k<($nbQuestion - 1); $k++) {
                    if($j == ($tab2[$k] + 1)) {
                        $id = $id + 1;
                    }
                }         
                $answer = new Answer();
                $answer->setProposition($tabProposition[$ctr])
                        ->setCorrection($tabCorrection[$ctr])
                        ->setQuestions($repo->find($id));

                        $manager->persist($answer);
                        $ctr++;
            }   
            $manager->flush();
            $this->addFlash(
                'success',
                "Enregistrement dans la base de données OK"
            );
            return $this->redirectToRoute('admin_answer_index');
            } else {
        
                $this->addFlash(
                    'danger',
                    "Le fichier que vous voulez charger n'existe pas ou sinon ce n'est pas un fichier Excel avec l'extension csv"
                );
            }
        
        }
         return $this->render('admin/load/loadcsv.html.twig', [
            'form' => $form->createView(),      
        ]);
    }

    /**
     * @Route("/admin/load/userQuizz", name="admin_load_userQuizz")
     */
    public function userQuizz(Request $request, EntityManagerInterface $manager, AnswerRepository $repo, QuestionRepository $questionRepo, UserRepository $userRepo)
    {
        $question = $questionRepo->findFirstId()[0]['id'];
        $id = $question;
        $tabPropo = [];
        for($i=0; $i<3; $i++) {
            $answers = $repo->findPropo($id)[$i];
            $answers = $answers['proposition'];
            $tabPropo[] = $answers;
        }
        $answer = new Answer();
        $form = $this->createForm(Quiz4Type::class, $answer, [
            'question' => $question,
            'tabPropo' => $tabPropo,
        ]);
        $form->handleRequest($request);
        $count = 0;
        $user = $this->getUser()->getId(); 
        $user = $userRepo->find($user);
        $ok = "";
        if($user->getOkquiz() != 0)
            $ok = true;
        else
            $ok = false;
        if($form->isSubmitted() && $form->isValid()) { 
            $correction = $repo->findByCorrection($question);  
            $correction = $correction[0]->getId();   
            $info = $form->getData();
            dd($info);
            $idProposition = $answer->getProposition();
            dd($idProposition);
            if($correction == $idProposition){
                $user->setOkquiz(true);
                $manager->persist($user);
                $manager->flush();
                $count++;  
                $this->addFlash(
                    'success',
                    "Vous avez une bonne réponse"
                );
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash(
                    'danger',
                    "Mauvaise réponse !"
                );
            }
            return $this->render('admin/quizz/userQuizz.html.twig', [
                'form' => $form->createView(),
                'count' => $count,
                'user' => $user,
                'ok' => $ok,
                'questions' => $questionRepo->findAll(),
            ]);
        }
    }
}
