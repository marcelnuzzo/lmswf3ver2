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
            
        $builder
            ->add('proposition');
        /*
        $typeForm = "";
        $choice = $options['choice'];
        for($i=0; $i<6; $i++) {
            
            if($choice[$i] == 'unique"') {
                $typeForm = 'unique';
            }
            elseif($choice[$i] == 'multiple"') {
                $typeForm = 'multiple';
            }
            elseif($choice[$i] == 'libre"') {
                $typeForm = 'libre';
            }
        }
        $propo = $options['propo'];
        
        for($i=0; $i<15; $i++) {
            if($typeForm == 'unique') {
            
                $builder
                    ->add('proposition', ChoiceType::class, [
                        'choices' => [
                            
                            'test' => 'choix',
                        ],
                        'expanded' => true,
                        'multiple' => false,
                    ])   
                    ;
            }
            elseif($typeForm == 'multiple') {
                $builder
                    ->add('proposition', ChoiceType::class, [
                        'choices' => [
                            
                            'test' => 'choix',
                        ],
                        'expanded' => true,
                        'multiple' => true,
                    ])   
                    ;
            }
            elseif($typeForm == 'libre') {
                $builder
                    ->add('proposition', TextType::class)
                    ;
            }    
        }
        */
        
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
