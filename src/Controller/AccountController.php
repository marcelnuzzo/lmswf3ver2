<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Entity\Role;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\RoleRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\envoiMail;

class AccountController extends AbstractController
{

    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ] );
    }


    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout() {
        //..rien !
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, EnvoiMail $envoiMail, RoleRepository $repo) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        $role = $repo->findAll();
        
        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $user->setOkquiz(false);
            //$role[1]->getTitle();
            //dd($role[1]);
            //$user->addUserRole($role[1]);
            $manager->persist($user);
            
            $manager->flush();
            $body="Utilisateur : ".$user->getFirstname().'</br>'."Email : ".$user->getEmail().'</br>'."Inscription confirmée";
                $message = $envoiMail->envoi($body);
                $mailer->send($message);

            $this->addFlash(
                'success',
                "Votre compte a bien été créer et un mail vous a été envoyé ! Vous pouvez maintenant vous connecter !"
            );
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profileEdit", name="account_profileEdit")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profilEdit(Request $request, EntityManagerInterface $manager) {
        
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('account/profileEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, EnvoiMail $envoiMail) {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password du user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $body="Utilisateur : ".$user->getFirstname().'</br>'."Email : ".$user->getEmail().'</br>'."Mot de passe modifié";
                $message = $envoiMail->envoi($body);
                $mailer->send($message);

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié et un mail vous a été envoyé."
                );

                return $this->redirectToRoute('homepage');
            }            
            
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * 
     * @Route("/account/index", name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function index() {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

}
