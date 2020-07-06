<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('birthAt', DateType::class, ['label' => 'Date de naissance'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('picture', TextType::class, ['label' => 'Photo'])
            ->add('hash', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('okquiz', CheckboxType::class, ['label' => 'Résultat quizz', 'required' => false])
            /*
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'title',
            ])
            */
            /*
            ->add('userRoles', CollectionType::class, [
                'entry_type' => RoleType::class,
                'entry_options' => ['label' => false],
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
