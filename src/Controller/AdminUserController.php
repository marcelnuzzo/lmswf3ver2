<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use App\Form\UserType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user_index")
     */
    public function index(UserRepository $repo, RoleRepository $roleRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
            'roles' => $roleRepository->findAll(),
        ]);
    }

     /**
     * @Route("/admin/user/new", name="admin_user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/admin/user/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $manager, RoleRepository $repo): Response
    {
       
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        /*
        $role = [];
        for($i=1; $i<=3; $i++) {
            $role[] = $repo->find($i)->getTitle();
        }
        */
      
        /*
        $toto = $role[0]->getTitle();
        dd($toto);
        */
        $role = new Role();
        $this->createForm(RoleType::class, $role);
        if ($form->isSubmitted() && $form->isValid()) {
             
           $role = $form['userRoles']->getData()[0];
           //dd($role);
           $user->addUserRole($role);
            
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
