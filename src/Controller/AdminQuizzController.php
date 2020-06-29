<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\Answer;
use App\Form\LoadType;
use App\Form\Quiz4Type;
use App\Form\QuizzType;
use App\Entity\Question;
use App\Service\Readfile;
use App\Repository\UserRepository;
use App\Repository\QuizzRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminQuizzController extends AbstractController
{
    /**
     * @Route("/admin/quizz", name="admin_quizz_index")
     */
    public function index(QuizzRepository $quizzRepository): Response
    {
        return $this->render('admin/quizz/index.html.twig', [
            'quizzs' => $quizzRepository->findAll(),
        ]);
    }

     /**
     * @Route("/admin/quizz/new", name="admin_quizz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quizz);
            $entityManager->flush();

            return $this->redirectToRoute('admin_quizz_index');
        }

        return $this->render('admin/quizz/new.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quizz/{id}", name="admin_quizz_show", methods={"GET"})
     */
    public function show(Quizz $quizz): Response
    {
        return $this->render('admin/quizz/show.html.twig', [
            'quizz' => $quizz,
        ]);
    }

    /**
     * @Route("/admin/quizz/{id}/edit", name="admin_quizz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quizz $quizz): Response
    {
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_quizz_index');
        }

        return $this->render('admin/quizz/edit.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/quizz/{id}", name="admin_quizz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quizz $quizz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quizz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_quizz_index');
    }
    
}
