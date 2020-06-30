<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Loadcsv
{
    public function getRead3($fichier)
    {
        //define("ECART", 2);
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($fichier);
        
        $row = 1;
        $ref = "";
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
        if (($handle = fopen($fichier, "r")) !== FALSE) {
            
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
                        $titre = $data[0];
                    }
                    if($row == 2) {
                        $ref = $data[0];
                    }
                    //echo "<p> $num champs à la ligne $row: <br /></p>\n";
                    $row++;
                    
                    if($flagProposition == 1 && $data[0] != $ref) {
                        if($data)
                        $tabProposition[] = $data[0];
                        $tabCorrection[] = $data[1];  
                        $nbProposition++;             
                    }
                    
                    for ($c=0; $c <$num; $c++) {
                                                   
                            //echo $data[$c] . "<br />\n"; 
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

        // tableau des nombres de propositions par question moins les questions libres
        $tab = [];
        // Calcul du nombre de propositions par question sans l'offset
        $diff = ""; 
        $tab[] = $line - $tabIndice[$nbQuestion-1];
        
        for($i=($nbQuestion-1); $i>0; $i--) {
            $diff = $tabIndice[$i] - $tabIndice[$i-1];    
            $tab[] = $diff - 2;
        } 
        $tab1 = array_reverse($tab);
        
        $tab2 = [];
        $tab2[] = $tab1[0];
        $var = 0;
        for($i=0; $i<$nbQuestion; $i++) {
            $var += $tab1[$i];
            $tab2[$i] = $var;
        }

        return [$nbQuestion, $tabQuestion, $choice, $nbProposition, $tab2, $tabProposition, $tabCorrection, $titre];
    }
}