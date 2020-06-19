<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\Answer;
use App\Form\QuizType;
use App\Form\TestType;
use App\Form\Quiz3Type;
use App\Form\Quiz5Type;
use App\Form\QuizzType;
use App\Form\Test2Type;
use App\Repository\QuizzRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/essaiform/{id}", name="quizz_essaiform")
     */
    /*
    public function essaiform(Quizz $quizz, QuizzRepository $repo, EntityManagerInterface $manager, Request $request, QuestionRepository $qRepo) {
        $quizz = $repo->find(3);
        $choix = $qRepo->findLibre();
        //dd($choix);
        $form = $this->createForm(QuizzType::class, $quizz);
        $form->handleRequest($request);
        //echo "test";
        //echo $_POST['name'];
        if ($form->isSubmitted() && $form->isValid()) {
            $info = $form->getData();
            //dd($info);
            echo("test");
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quizz);
            $entityManager->flush();

            //return $this->redirectToRoute('quizz_index');
        }


        return $this->render('quizz/essaiform.html.twig', [
            'form' => $form->createView(),
            'quizz' => $quizz,
            'choix' => $choix,
        ]);
    }
    */

    /**
     * @Route("/list", name="quizz_list", methods={"GET"})
     */
    public function listquizz(QuizzRepository $repo): Response
    {
        return $this->render('quizz/list.html.twig', [
            'quizzs' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quizz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quizz = new Quizz();
        $form = $this->createForm(Quiz5Type::class, $quizz);
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
     * @Route("/toutvoir/{id}", name="quizz_toutvoir", methods={"GET","POST"})
     */
    public function toutvoir($id, Request $request, QuestionRepository $repo, AnswerRepository $answerRepo, EntityManagerInterface $manager, Quizz $quizz)
    {
        
        $firstQuestion = $repo->findFirstId()[0]['id'];
        $countQuestion = $repo->findCountQuestion();
        for($id=$firstQuestion; $id<($firstQuestion + $countQuestion); $id++) {
            $question[] = $repo->find($id);
            $choice[] = $repo->findByQId($id)[0]['choice'];
        }

        
        $countPropos = $answerRepo->findCountPropo();
        for($i=0; $i<$countPropos; $i++) {
            $propo[] = $answerRepo->findProposition()[$i]['proposition'];
        }
        $propo = $propo[0];
        
        $id = $repo->findFirstId()[0]['id'];
        
        $answer = new Answer();
        
        $this->createForm(Test2Type::class, $answer, [
            'propo' => $propo,
            'choice' => $choice,
        ]);
        
        $form = $this->createForm(QuizType::class, $quizz);
        
        $proposition[] = $answerRepo->findOnePropo($id)[0]['proposition'];
        
        $proposition = $proposition[0];
        //dd($proposition);
        /*
        $answer = new Answer();
        $this->createForm(Quiz3Type::class, $answer, [
            'proposition' => $proposition,
        ]);
        /*
        $form = $this->createForm(QuizzType::class, $quizz);
        */
        $form->handleRequest($request);
        //dd($form);

        if($form->isSubmitted() && $form->isValid()) {
           
            $manager->persist($quizz);
            $manager->flush();

            return $this->redirectToRoute('quizz_index');
        }

        return $this->render('quizz/toutvoir.html.twig', [      
            'form' => $form->createView(),
            'questions' => $repo->findAll(),
           
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
        $form = $this->createForm(Quiz5Type::class, $quizz);
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
     * @Route("/voir/{id}", name="quizz_voir", methods={"GET","POST"})
     */
    /*
    public function voir($id, Request $request, QuestionRepository $repo, AnswerRepository $answerRepo): Response
    {
        $question = $repo->find($id);
        $choice = $repo->findByQId($id);
        $propo = $answerRepo->findPropo($id);
        //dd($propo);
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
    */
    
}
