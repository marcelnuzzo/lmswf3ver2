<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Testquiz;
use App\Repository\QuestionRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TestquizController extends AbstractController
{
    /**
     * @Route("/testquiz", name="testquiz")
     */
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

    /**
     * @Route("/testload", name="testquiz_testload")
     */
    public function testload(EntityManagerInterface $manager, QuestionRepository $repo)
    {
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load("c://wamp64/www/Quizz.csv");
        $row = 1;
        // Tableau où l'on récupère les questions (label)
        $tabQuestion = [];
        // On compte le nombre de questions
        $nbQuestion = 0;
        // On récupère le type de question (choix unique, choix multiple ...)
        $choice = [];
        // Tableau où l'on récupère les propositions
        $tabProposition = [];
        // Tableau où l'on récupère les corrections
        $tabCorrection = [];
        // Dès qu'on récupère la question on monte le flag
        $flagQuestion = 0;
        // Dès qu'on récupère le flagQuestion on monte le flag
        $flagProposition = 0;
        // Tableau où l'on récupère les numéros de ligne des questions ($row)
        $tabIndice = [];

        $nbProposition = 0;
        $line = "";
        // On ouvre le fichier de type csv
        if (($handle = fopen("c://wamp64/www/Quizz.csv", "r")) !== FALSE) {
            // On récupère les éléments séparés par un point virgule
            while (($data = fgetcsv($handle, 1000, ";", "'")) !== FALSE) {
                // Si la ligne n'est pas vide
                if(!(is_null($data))) {
                    // Si le premier élément n'existe pas, on sort de la boucle
                    if($data[0] == null) {
                        break 1;
                    }
                    $num = count($data);              
                    
                    if($row == 1) {
                        $ref = $data[0];
                    }
                    echo "<p> $num champs à la ligne $row: <br /></p>\n";
                    $row++;
                    
                    if($flagProposition == 1 && $data[0] != $ref) {
                        if($data)
                        $tabProposition[] = $data[0];
                        $tabCorrection[] = $data[1];  
                        $nbProposition++;             
                    }
                    
                    for ($c=0; $c < $num; $c++) {
                                                    
                            echo $data[$c] . "<br />\n"; 

                            if($flagQuestion == 1) {
                                $tabQuestion[] = $data[$c];
                                $choix = $data[$c+1];
                                if($choix != 'libre"') {
                                    $choice[] = $data[$c+1];
                                    $flagQuestion = 0;
                                    $flagProposition = 1;
                                } else {
                                    $choice[] = $data[$c+1];
                                    $flagQuestion = 0;
                                }
                            }
                            
                            if($data[0] == $ref) {
                                                    
                                $nbQuestion++;
                                $flagQuestion = 1;
                                $tabIndice[] = $row;
                                
                                $flagProposition = 0;
                            }
                            
                    }
                } 
            }
            $line = $row-1;
            
                fclose($handle);
            
        }
        // Ligne Question + le label de la question à supprimer
        define("OFFSET", 2);
        // tableau des nombres de propositions par question moins les questions libres
        $tab = [];
        // Calcul du nombre de propositions par question sans l'offset
        $diff = ""; 
        $tab[] = $line - $tabIndice[$nbQuestion-1];
        
        for($i=($nbQuestion-1); $i>0; $i--) {
            $diff = $tabIndice[$i] - $tabIndice[$i-1];    
            $tab[] = $diff - OFFSET;
        } 
        $tab1 = array_reverse($tab);
        
        $tab2 = [];
        $tab2[] = $tab1[0];
        $var = 0;
        for($i=0; $i<$nbQuestion; $i++) {
            $var += $tab1[$i];
            $tab2[$i] = $var;
        }

        //dd($tab2[1]);
        
        for($i=0; $i<$nbQuestion; $i++) {
            $question = new Question();
            $question->setLabel($tabQuestion[$i])
                    ->setChoice($choice[$i]);

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
        
         return $this->render('testquiz/testload.html.twig', [
            'controller_name' => 'TestquizController',
            'spreadsheet' => $spreadsheet,
            'num' => $num,
            'tabQuestion' => $tabQuestion,
            'choice' => $choice,
            'tabProposition' => $tabProposition,
            'tabCorrection' => $tabCorrection,
            'tabIndice' => $tabIndice,
        ]);
    }
}
