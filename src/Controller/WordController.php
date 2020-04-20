<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\Team;
use App\Entity\Word;
use App\Form\WordType;
use App\Repository\RoundRepository;
use App\Repository\WordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WordController extends AbstractController
{
    /**
     * @Route("/word/", name="word_index", methods={"GET"})
     * @param WordRepository $wordRepository
     * @return Response
     */
    public function index(WordRepository $wordRepository): Response
    {
        return $this->render('word/index.html.twig', [
            'words' => $wordRepository->findAll(),
        ]);
    }

    /**
     * @Route("game/{id}/newword", name="add_words_to_game", methods={"GET","POST"})
     * @param Game $game
     * @param WordRepository $choices
     * @param RoundRepository $rounds
     * @param Request $request
     * @return Response
     */
    public function new(Game $game, WordRepository $choices, RoundRepository $rounds, Request $request): Response
    {
        // number words to add for each team
        $nbteams = count($game->getTeams());
        $nbwords = round($game->getNbWords() / $nbteams);

        // find words already add by the team
        $words = $rounds->findLinesFromOneCreator($game, $this->getUser());
        $nb = count($words);
        $list=[];
        foreach($words as $word) {
            $choice = $word->getWord()->getWord();
            array_push($list, $choice);
        }

        // create form to add word
        $word = new Word();
        $form = $this->createForm(WordType::class, $word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $round = new Round();
            $round
                ->setGame($game)
                ->setWord($word)
                ->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($word);
            $entityManager->persist($round);
            $entityManager->flush();

            return $this->redirectToRoute('add_words_to_game', [
                'id' => $game->getId(),
            ]);
        }

        return $this->render('word/new.html.twig', [
            'game' => $game,
            'word' => $word,
            'list' => $list,
            'nbwords' => $nbwords,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/word/{id}", name="word_show", methods={"GET"})
     */
    public function show(Word $word): Response
    {
        return $this->render('word/show.html.twig', [
            'word' => $word,
        ]);
    }

    /**
     * @Route("/word/{id}/edit", name="word_edit", methods={"GET","POST"})
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
     * @Route("/word/{id}", name="word_delete", methods={"DELETE"})
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
