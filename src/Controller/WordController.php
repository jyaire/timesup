<?php

namespace App\Controller;

use App\Entity\Word;
use App\Form\WordType;
use App\Repository\WordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/word")
 */
class WordController extends AbstractController
{
    /**
     * @Route("/", name="word_index", methods={"GET"})
     */
    public function index(WordRepository $wordRepository): Response
    {
        return $this->render('word/index.html.twig', [
            'words' => $wordRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="add_words_to_game", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $word = new Word();
        $form = $this->createForm(WordType::class, $word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($word);
            $entityManager->flush();

            return $this->redirectToRoute('word_index');
        }

        return $this->render('word/new.html.twig', [
            'word' => $word,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="word_show", methods={"GET"})
     */
    public function show(Word $word): Response
    {
        return $this->render('word/show.html.twig', [
            'word' => $word,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="word_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Word $word): Response
    {
        $form = $this->createForm(WordType::class, $word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('word_index');
        }

        return $this->render('word/edit.html.twig', [
            'word' => $word,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="word_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Word $word): Response
    {
        if ($this->isCsrfTokenValid('delete'.$word->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($word);
            $entityManager->flush();
        }

        return $this->redirectToRoute('word_index');
    }
}
