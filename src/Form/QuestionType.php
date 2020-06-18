<?php

namespace App\Form;

use App\Form\TestType;
use App\Form\Quiz3Type;
use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        //->add('label')  
        ->add('answers', CollectionType::class, [
            'label' => false,
            'entry_type' => Quiz3Type::class,
            'entry_options' => ['label' => false],
        ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
