<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\AssignRoleType;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RoleController extends AbstractController
{
    /**
     * @Route("/role", name="role_index", methods={"GET"})
     */
    public function index(RoleRepository $roleRepository, UserRepository $repo): Response
    {
        return $this->render('role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
            'users' => $repo->findAll(),
        ]);
    }

     /**
     * @Route("listUser", name="role_listUser", methods={"GET"})
     */
    public function listUser(UserRepository $repo): Response
    {
        return $this->render('role/list.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("listUser/{id}", name="role_", methods={"GET", "POST"})
     */
    public function assign(Request $request, UserRepository $repo)
    {
        $role = new Role();
        $form = $this->createForm(AssignRoleType::class, $role);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            dd($form);
        
            //return $this->redirectToRoute('role_index');
        }
        return $this->render('role/assign.html.twig', [
            'form' => $form->createView(),
            'user' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/role/new", name="role_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('role/new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/role/{id}", name="role_show", methods={"GET"})
     */
    public function show(Role $role): Response
    {
        return $this->render('role/show.html.twig', [
            'role' => $role,
        ]);
    }

    /**
     * @Route("/role/{id}/edit", name="role_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('role_index');
        }

        return $this->render('role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/role/{id}", name="role_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Role $role): Response
    {
        if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($role);
            $entityManager->flush();
        }

        return $this->redirectToRoute('role_index');
    }

}
