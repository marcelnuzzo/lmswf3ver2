<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\Answer;
use App\Form\TestType;
use App\Form\QuizzType;
use App\Repository\QuizzRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quizz")
 */
class QuizzController extends AbstractController
{
    /**
     * @Route("/", name="quizz_index", methods={"GET"})
     */
    public function index(QuizzRepository $quizzRepository): Response
    {
        return $this->render('quizz/index.html.twig', [
            'quizzs' => $quizzRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quizz_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/new.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quizz_show", methods={"GET"})
     */
    public function show(Quizz $quizz): Response
    {
        return $this->render('quizz/show.html.twig', [
            'quizz' => $quizz,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quizz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quizz $quizz): Response
    {
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/edit.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quizz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quizz $quizz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quizz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quizz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quizz_index');
    }

    /**
     * @Route("/montre/{id}", name="quizz_montre", methods={"GET","POST"})
     */
    public function montre(Request $request, Quizz $quizz, AnswerRepository $repo): Response
    {
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);
        //$this->createForm(TestType::class, $answer);
        $answer = new Answer();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/montre.html.twig', [
            'quizz' => $quizz,
            'form' => $form->createView(),
            'answer' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/voir/{id}", name="quizz_voir", methods={"GET","POST"})
     */
    public function voir($id, Request $request, QuestionRepository $repo, AnswerRepository $answerRepo): Response
    {
        $question = $repo->find($id);
        $choice = $repo->findByQId($id);
        $propo = $answerRepo->findPropo($id);
        //dd($choice[0]['choice']);
        $answer = new Answer();
        $form = $this->createForm(TestType::class, $answer, [
            'choice' => $choice,
            "propo" => $propo,
        ]);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/voir.html.twig', [
            
            'form' => $form->createView(),
            'answer' => $answer,
            'question' => $question
        ]);
    }
}
