<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Point;
use App\Entity\Round;
use App\Entity\Team;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\PointRepository;
use App\Repository\RoundRepository;
use App\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Datetime;

/**
 * @Route("/game")
 * @IsGranted("ROLE_USER")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_index", methods={"GET"})
     * @param GameRepository $gameRepository
     * @return Response
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
        $game
            ->setDate(new Datetime())
            ->addTeam($team)
            ->setNbWords(40)
            ->setIsFinished(0)
        ;
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
     * @Route("/wait", name="game_wait")
     */
    public function wait()
    {
        $game = $this->getUser()->getGame();
        return $this->render('game/wait.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/play", name="game_play")
     * @param RoundRepository $rounds
     * @return Response
     */
    public function play(RoundRepository $rounds)
    {
        $game = $this->getUser()->getGame();
        $words = $rounds->findLinesRound1($game);
        $round = 1;
        if(empty($words)) {
            $words = $rounds->findLinesRound2($game);
            $round = 2;
            if(empty($words)) {
                $words = $rounds->findLinesRound3($game);
                $round = 3;
                if(empty($words)) {
                    return $this->redirectToRoute('game_end');
                }
            }
        }

        // random and give one Round object
        shuffle($words);
        $word = $words[0];

        return $this->render('game/play.html.twig', [
            'game' => $game,
            'word' => $word,
            'round' => $round,
        ]);
    }

    /**
     * @Route("/wincard/{word}/team/{team}/round/{roundnb}", name="game_wincard")
     * @param Round $word
     * @param Team $team
     * @param int $roundnb
     * @return RedirectResponse
     */
    public function wincard(Round $word, Team $team, int $roundnb)
    {
        switch($roundnb) {
            case 1:
                $round = $word->setRound1winner($team);
                break;
            case 2:
                $round = $word->setRound2winner($team);
                break;
            case 3:
                $round = $word->setRound3winner($team);
                break;
        }

        // give point to team who find word
        $game = $this->getUser()->getGame();
        $pointFinder = new Point();
        $pointFinder
            ->setGame($game)
            ->setTeam($team)
            ->setPoint(1)
            ->setRoundnb($roundnb);

        // give point to team who help to find word
        $teamHelper = $this->getUser();
        $pointHelper = new Point();
        $pointHelper
            ->setGame($game)
            ->setTeam($teamHelper)
            ->setPoint(1)
            ->setRoundnb($roundnb);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($round);
        $entityManager->persist($pointFinder);
        $entityManager->persist($pointHelper);
        $entityManager->flush();

        return $this->redirectToRoute('game_play');
    }

    /**
     * @Route("/end", name="game_end")
     * @param PointRepository $points
     * @return Response
     */
    public function end(PointRepository $points)
    {
        $game = $this->getUser()->getGame();
        $points = $points->findBy([
            'game' => $game,
        ]);

        return $this->render('game/end.html.twig', [
            'game' => $game,
            'points' => $points,
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
     * @Route("/{id}/add/{team}", name="add_team_to_game")
     * @param Game $game
     * @param Team $team
     * @return RedirectResponse
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
     * @Route("/{id}/delete/{team}", name="delete_team_from_game")
     * @param Game $game
     * @param Team $team
     * @return RedirectResponse
     */
    public function deleteTeamFromGame(Game $game, Team $team)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game->removeTeam($team);
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->redirectToRoute('game_show', [
            'id' => $game->getId(),
        ]);
    }

    /**
     * @Route("/validate/{id}", name="validate_game")
     * @param Game $game
     * @return RedirectResponse
     */
    public function validateGame(Game $game)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game->setIsComposed(1);
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
     * @param Request $request
     * @param Game $game
     * @return Response
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
