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
        $round = new Round();
        $form = $this->createForm(RoundType::class, $round);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $round = $form->getData();
            $round
                ->setGame($game)
                ->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($round);
            $entityManager->flush();

            return $this->redirectToRoute('add_words_to_game', [
                'id' => $game->getId(),
            ]);
        }

        return $this->render('game/newword.html.twig', [
            'game' => $game,
            'list' => $list,
            'nbwords' => $nbwords,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("game/{game}/deleteword/{id}", name="round_delete")
     * @param Game $game
     * @param Round $word
     * @return Response
     */
    public function delete(Game $game, Round $word): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($word);
        $entityManager->flush();

        return $this->redirectToRoute('add_words_to_game', [
            'id' => $game->getId(),
        ]);
    }
}
