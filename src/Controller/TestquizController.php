<?php

namespace App\Controller;

use App\Entity\Testquiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;


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
    public function testload()
    {
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load("c://wamp64/www/Quizz.csv");
        $row = 1;
        $tabQuestion = [];
        $nbQuestion = 0;
        $choice = [];
        $proposition = [];
        $correction = [];
        $flagQuestion = 0;
        $flagProposition = 0;
        if (($handle = fopen("c://wamp64/www/Quizz.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";", "'")) !== FALSE) {
                $num = count($data);              
                
                if($row == 1) {
                    $ref = $data[0];
                }
                echo "<p> $num champs à la ligne $row: <br /></p>\n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                                                    
                        echo $data[$c] . "<br />\n";
                        if($flagProposition == 1) {
                            $proposition[] = $data[$c];
                            $flagProposition = 0;
                        }

                        if($flagQuestion == 1) {
                            $tabQuestion[] = $data[$c];
                            $choice[] = $data[$c+1];
                            $flagQuestion = 0;
                            $flagProposition = 1;
                        }
                        
                        if($data[$c] == $ref) {                        
                            $nbQuestion++;
                            $flagQuestion = 1;
                        }
                        
                }
                    
            }
            fclose($handle);
        }
        echo $nbQuestion;
        var_dump($tabQuestion);
        var_dump($choice);
        var_dump($proposition);
      
         return $this->render('testquiz/testload.html.twig', [
            'controller_name' => 'TestquizController',
            'spreadsheet' => $spreadsheet,
            'num' => $num
        ]);
    }
}
