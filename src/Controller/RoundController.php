<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Round;
use App\Form\RoundType;
use App\Repository\RoundRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoundController extends AbstractController
{
    /**
     * @Route("/round/", name="round_index", methods={"GET"})
     * @param RoundRepository $roundRepository
     * @return Response
     */
    public function index(RoundRepository $roundRepository): Response
    {
        return $this->render('word/index.html.twig', [
            'rounds' => $roundRepository->findAll(),
        ]);
    }

    /**
     * @Route("game/{id}/newword", name="add_words_to_game", methods={"GET","POST"})
     * @param Game $game
     * @param RoundRepository $rounds
     * @param Request $request
     * @return Response
     */
    public function new(Game $game, RoundRepository $rounds, Request $request): Response
    {
        // redirect if team is not composed
        if (($game->getIsComposed() == false)) {
            return $this->redirectToRoute('game_show', [
                'id' => $game->getId(),
            ]);
        }

        // number words to add for each team
        $nbteams = count($game->getTeams());
        $nbwords = round($game->getNbWords() / $nbteams);

        // find words already add by the team
        $list = $rounds->findLinesFromOneCreator($game, $this->getUser());

        // create form to add word
        $word = new Round();
        $form = $this->createForm(RoundType::class, $word);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $round = new Round();
            $round
                ->setGame($game)
                //->setWord($form->get('word')->getData())
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
     * @param Round $word
     * @return Response
     */
    public function show(Round $word): Response
    {
        return $this->render('word/show.html.twig', [
            'word' => $word,
        ]);
    }

    /**
     * @Route("/word/{id}/edit", name="word_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Round $round
     * @return Response
     */
    public function edit(Request $request, Round $round): Response
    {
        $form = $this->createForm(RoundType::class, $round);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('round_index');
        }

        return $this->render('word/edit.html.twig', [
            'round' => $round,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("game/word/{id}", name="word_delete", methods={"DELETE"})
     * @param Request $request
     * @param Round $word
     * @return Response
     */
    public function delete(Request $request, Round $word): Response
    {
        if ($this->isCsrfTokenValid('delete'.$word->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($word);
            $entityManager->flush();
        }

        return $this->redirectToRoute('round_index');
    }
}
