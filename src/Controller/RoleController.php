<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\envoiMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoleController extends AbstractController
{
    /**
     * @Route("/role", name="role")
     */
    public function index(RoleRepository $repo)
    {
        return $this->render('role/index.html.twig', [
            'roles' => $repo->findAll(),
        ]);
    }

    /**
    * @Route("/listerole", name="role_listerole")
    */
    public function listerole()
    {
        
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
    
        return $this->render('role/listerole.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/editRole/{id}", name="role_editRole")
     */
    public function editRole(\Swift_Mailer $mailer,User $user, Request $request, EntityManagerInterface $manager, envoiMail $envoiMail) 
    {
        /*
        $form = $this->createFormBuilder($user)
            ->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'label' => false,
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Super admin' => 'ROLE_SUPER_ADMIN',
                        'User' => 'ROLE_USER'
                    ],
                ],
            ])
            ->getForm();

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $manager->persist($user);         
                $manager->flush();
                
                $body="Username : ".$user->getUsername().'</br>'."Vous avez le role de : ".$user->getRoles()[0];          
                $message = $envoiMail->envoi($body);
                $mailer->send($message);
                
                return $this->redirectToRoute('listeRole',['id' => $user->getId()
                ]);
            }
        */
        return $this->render('role_editRole.html.twig', [
            //'form' => $form->createView(),
        ]);
    }
}
