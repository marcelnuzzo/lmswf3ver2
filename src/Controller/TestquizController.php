<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quizz;
use App\Entity\Testquiz;
use App\Form\LoadCsvType;
use App\Repository\QuestionRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Loadcsv;

class TestquizController extends AbstractController
{
  
    /**
     * @Route("/testquiz", name="testquiz")
     */
    /*
    public function index(EntityManagerInterface $manager)
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load("C://wamp64/www/testquiz.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
       
        $getHighestColumn = $spreadsheet->setActiveSheetIndex(0)->getHighestColumn();
        $getHighestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
        $nbCol = hexdec($getHighestColumn) - 9;
        $alpha='A';
        $valCellX=[];
        $count=0;
        
            
        for($i=1; $i<=$getHighestRow; $i++) { 
            
            for($j=1; $j<=$nbCol; $j++) {              
                $valCellX[] = $sheet->getCell($alpha.$i)->getValue($j);        
                ++$alpha;
                $count++;
            }
            if($alpha == "D")
                $alpha = "A";
               
        }
        
        $ctr=0;
        for($i=1; $i<=$getHighestRow; $i++) {
            $testquiz = new Testquiz();
            $testquiz->setQuestion($valCellX[$ctr]);
            $ctr++;
            $testquiz->setProposition($valCellX[$ctr]);
            $ctr++;
            $testquiz->setCorrection($valCellX[$ctr]);
            $ctr++;
            $manager->persist($testquiz);
           
        }
        $manager->flush();

        return $this->render('testquiz/index.html.twig', [
            'controller_name' => 'TestquizController',
        ]);
    }
    */

    /**
     * @Route("/loadcsv", name="testquiz_loadcsv")
     */
    public function loadcsv(EntityManagerInterface $manager, QuestionRepository $repo, Request $request, Loadcsv $loadcsv)
    {
        $form = $this->createForm(LoadCsvType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $donnee = $form->getData();
            $fichier = $donnee['Chargement'];
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
            return $this->redirectToRoute('homepage');
        }
         return $this->render('testquiz/loadcsv.html.twig', [
            'form' => $form->createView(),      
        ]);
    }
}
