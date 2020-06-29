<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
       /*
        ->add('title', TextType::class, [
            'label' => 'Titre',
            'attr' => [
                'placeholder' => 'Veuillez insérer un nouveau rôle',
            ]
        ])
        */
        /*
        ->add('title', ChoiceType::class, [
            'choices' => [
                'ROLE_SUPER_ADMIN' => 'role super admin',
                'ROLE_ADMIN' => 'role admin',
                'ROLE_USER' => 'role user', 
            ],
            'expanded' => true,
            'multiple' => true,
        ])
        */
        
        ->add('title')
        
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
