<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Datetime;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_index", methods={"GET"})
     */
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="game_new")
     */
    public function new()
    {
        // new Game with date now and actual team
        $game = new Game();
        $team = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $game->setDate(new Datetime())->addTeam($team)->setNbWords(40);
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->redirectToRoute('game_show', [
            'id' => $game->getId(),
        ]);
    }

    /**
     * @Route("/join", name="game_join", methods={"GET","POST"})
     * @param Request $request
     * @param GameRepository $games
     * @return Response
     */
    public function join(Request $request, GameRepository $games): Response
    {
        $form = $this->createFormBuilder()
            ->add('game')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $idgame = $form->getData()['game'];
            $game = $games->findOneBy(['id'=>$idgame]);
            $game->addTeam($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_show', [
                'id' => $game->getId(),
            ]);
        }

        return $this->render('game/join.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/play", name="game_play")
     */
    public function play()
    {
        $game = $this->getUser()->getGame();

        return $this->render('game/play.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/{id}", name="game_show", methods={"GET"})
     * @param Game $game
     * @param TeamRepository $teams
     * @return Response
     */
    public function show(Game $game, TeamRepository $teams)
    {
        $teams = $teams->findTeamsNotInGame($game);

        return $this->render('game/show.html.twig', [
            'game' => $game,
            'teams' => $teams,
        ]);
    }

    /**
     * @Route("{id}/add/{team}", name="add_team_to_game")
     * @param Game $game
     * @param Team $team
     */
    public function addTeamToGame(Game $game, Team $team)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game->addTeam($team);
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->redirectToRoute('game_show', [
            'id' => $game->getId(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="game_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function edit(Request $request, Game $game): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Game $game): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_index');
    }
}
