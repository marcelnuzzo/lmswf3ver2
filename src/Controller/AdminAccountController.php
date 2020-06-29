<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\envoiMail;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }


    /**
     * Permet de se déconnecter
     *
     * @Route("/admin/logout", name="admin_account_logout")
     * 
     * @return void
     */
    public function logout() {
        // ...
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/admin/account/profileEdit", name="admin_account_profileEdit")
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
        
        return $this->render('admin/account/profileEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/admin/account/password-update", name="admin_account_password")
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

        return $this->render('admin/account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * 
     * @Route("/admin/account/index", name="admin_account_index")
     * 
     * @return Response
     */
    public function index() {
        return $this->render('admin/account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

}
