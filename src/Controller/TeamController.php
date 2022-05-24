<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Team;
use App\Entity\User;
use App\Form\TeamType;
use App\Repository\ActivityRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Team as EntityTeam;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("coach/team")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/", name="app_team_index", methods={"GET"})
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_team_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TeamRepository $teamRepository, UserInterface $user, EntityManagerInterface $doctrine): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        // l'objet Activity est obligatoire car team->addActivity() attend un objet Activity en param
        $activity = new Activity();

    
        if ($form->isSubmitted() && $form->isValid()) {
            //$team->addActivity($activity);
            $teamRepository->add($team);

            // je set l'objet activity avec :
            // - $user courant de userInterface
            // - $team qui est l'équipe fraichement créée 
            // - $role 1 = entraîneur qui n'est pas fixé chez moi en bdd
            $activity->setUser($user);
            $activity->setTeam($team);
            $activity->setRole(1); 
    
            $newActivity = $team->addActivity($activity);
            $doctrine->persist($newActivity);
            $doctrine->persist($activity);
            $doctrine->persist($user);
           
            $doctrine->flush(); 

            return $this->redirectToRoute('coach_teams', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    

    /**
     * @Route("/{id}", name="app_team_show", methods={"GET"})
     */
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_team_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamRepository->add($team);
            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    /** Delete player from my team
     * @Route("/delete/{id}", name="app_team_delete_player_from_team", methods={"POST"})
     */
    public function deletePlayerFromTeam(Request $request, Activity $activity, ActivityRepository $activityRepository, Security $security): Response
    {

    
    // ? est ce que la personne qui essaie de supprimer est bien entraineur de l'équipe ?
    // récupère le user courant
    $user = $security->getUser();

    // récupere toutes les activity du user courant dans l'équipe du joueur qu'on tente de supprimer
    // tableau ? oui le joueur peut avoir plusieurs activity dans une équipe car plusieurs roles
    $userActivityInThisTeam = $activityRepository->findBy([
        'user' => $user->getId(),
        'team' => $activity->getTeam()->getId()
        ] );
    
    // check si il a les droits  
    $haveRights = false;
    foreach ($userActivityInThisTeam as $user) {
        if ($user->getRole() === 1) {
            $haveRights = true;
        }
    }  

    if ($haveRights) {
        // !!! Formulaire delete dans le twig !!!
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {
            $activityRepository->remove($activity);
            return $this->redirectToRoute('coach_teams', [], Response::HTTP_SEE_OTHER);
        }
    }

    // sinon tu renvois une erreur !!!
        return $this->redirectToRoute('coach_teams', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_team_delete", methods={"POST"})
     */
    public function delete(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $teamRepository->remove($team);
        }

        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }



    
}
