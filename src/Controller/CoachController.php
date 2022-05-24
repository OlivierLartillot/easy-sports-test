<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\Team;
use App\Entity\User;
use App\Form\ResultType;
use App\Repository\ActivityRepository;
use App\Repository\ResultRepository;
use App\Repository\TeamRepository;
use App\Repository\TestRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/coach")
 */

class CoachController extends AbstractController
{
    /**
     * @Route("/", name="app_coach")
     */
    public function index(): Response
    {
        return $this->render('coach/home_coach.html.twig', [
            'controller_name' => 'CoachController',
        ]);
    }

     /**
     * @Route("/teams", name="coach_teams")
     */   
    public function teams(UserRepository $userRepository, ActivityRepository $activityRepository, UserInterface $currentUser): Response
    {
        //je récupère l'id du user courant
        $id = $currentUser->getId(); 
        $user = $userRepository->find($id);

       // je récupère toutes les équipes de l'utilisateur
        $myTeams = $user->getActivities();
        /* J'exclus les équipes dont l'entraîneur n'est que joueur :
            - j'enregistre les id des équipes coach dans le tableau $teamsIdList 
            - si le role du coach dans cet équipe est 0 (joueur) 
              alors tu ne l'enregistres pas (car on a besoin que des équipes "entrainées") 
        */

        $teamsIdList = [];

        foreach ($myTeams as $team) {

            // si il est enraineur de l'équipe
            

                // enregistre l'id de l'équipe dans le tableau
                $teamsIdList [] = $team->getTeam()->getId();
            

        } 

        /* Grace au tableau contenant les id des équipes du coach 
           Je vais créer un autre tableau $teamPlayersListByTeam qui va prendre en :
               - key   = l'id de l'équipe
               - value = la liste des joueurs
        */

        $teamPlayersListByTeam = [];
        foreach ($teamsIdList as $teamId) {
            
            // récupère la liste des users de cette équipe
            $players = $activityRepository->findBy(['team' => $teamId]);
            // si le nombre de personne de cette équipe = 1 c'est qu'il n'y a que l'entraineur ...)
            $countUsersNumber = count($players);

                $teamPlayersListByTeam [] = $players;    
        }

        // récupère les équipes grace a la propriété de user 

        return $this->render('common/team.html.twig', [
            'myTeams' => $myTeams,
            'teamPlayersListByTeam' => $teamPlayersListByTeam,
            'user'=> $user
        ]);
    }

    /**
     * @Route("/teams/{id}/tests", name="test_team", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function testByTeam(int $id,Team $team, ActivityRepository $activityRepository, EntityManagerInterface $manager,UserRepository $user, Request $request, SessionInterface $session, UserInterface $userInterface)
    {   
        $result = new Result();
        $players = $activityRepository->findBy(['team'=>$id]);
        $form = $this->createForm(ResultType::class, $result);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $result->setTest($test);
            $result->setDoneAt( new DateTime('now'));
            $u = $user->findBy(['id'=>$session->get('player')]);
            $result->setUser($u[0]);
            if(in_array("ROLE_COACH",$userInterface->getRoles())){
                $result->setStatus(1);
            }else{
                $result->setStatus(0);
            }
            
            $manager->persist($result);
            $manager->flush();

            return $this->redirectToRoute('user_home',['slug'=>$userInterface->getSlug()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('common/test_team.html.twig', [
            // 'test' => $test,
            'form' => $form,
            'players'=>$players,
            'team'=>$team
        ]);
    }

    /**
     * @Route("/teams/history", name="teams_history")
     *
     * @return void
     */
    public function historyByTeam(UserInterface $user, TestRepository $testRepository, ResultRepository $resultDepository,UserRepository $userRepository)
    {   
        $allTests = $testRepository->eightTests();
        foreach($allTests as $test){
            $alltestsId [] = $test->getId();
        }
        $activities = $user->getActivities();
        foreach($activities as $activity){
            $teamsId [] = $activity->getTeam()->getId();
        }
        foreach($teamsId as $id){
            $allUsers [] = $userRepository->getUsersAndTeamByTeamId($id);
            // foreach($allUsers as $user){
            //     $resultDepository->
            // }
        }
        
            return $this->render('/common/teams_history.html.twig',[
            'teams' => $allUsers,
            'tests' => $allTests,
        ]);
    }
       
}






