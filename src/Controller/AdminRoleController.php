<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminRoleController extends AbstractController
{
    /**
     * @Route("/admin/role", name="admin_role_index")
     */
    public function index(RoleRepository $roleRepository)
    {
        return $this->render('admin/role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

     /**
     * @Route("/admin/role/new", name="admin_role_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('admin_role_index');
        }

        return $this->render('admin/role/new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/role/{id}", name="admin_role_show", methods={"GET"})
     */
    public function show(Role $role): Response
    {
        return $this->render('admin/role/show.html.twig', [
            'role' => $role,
        ]);
    }

     /**
     * @Route("/admin/role/{id}/edit", name="admin_role_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_role_index');
        }

        return $this->render('admin/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/role/{id}", name="admin_role_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Role $role): Response
    {
        if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($role);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_role_index');
    }

}
