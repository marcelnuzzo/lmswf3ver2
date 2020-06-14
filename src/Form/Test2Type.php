<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Test2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choice = $options['choice'];
        $propo = $options['propo'];
        $expanded = true;
        $multiple = true;
        $typeForm = "";
        if($choice[0] == 'unique"') {
            $multiple = false;
            $expanded = true;
            $typeForm = 'choix';
        }
        elseif($choice[0]['choice'] == 'multiple"') {
            $multiple = true;
            $expanded = true;
            $typeForm = 'choix';
        }
        elseif($choice[0]['choice'] == 'libre"') {
            $multiple = false;
            $expanded = false;
            $typeForm = 'libre';
        }
        
            if($typeForm == 'choix') {
            
                $builder
                    ->add('proposition', ChoiceType::class, [
                        'choices' => [
                            
                            $propo[0] => 'choix 1',
                            $propo[1] => 'choix 2',
                            $propo[2] => 'choix 3',
                        ],
                        'expanded' => $expanded,
                        'multiple' => $multiple,
                    ])   
                    ;
            }
            elseif($typeForm == 'libre') {
                $builder
                    ->add('proposition', TextType::class)
                    ;
            }    
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'choice' => null,
            'propo' => null,
        ]);
    }
}
