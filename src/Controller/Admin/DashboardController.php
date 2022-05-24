<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Entity\TagTest;
use App\Entity\Test;
use App\Entity\User;
use App\Repository\ResultRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    protected $userRepository;
    protected $resultRepository;

    public function __construct(UserRepository $userRepository, ResultRepository $resultRepository) {

        $this->userRepository = $userRepository;
        $this->resultRepository = $resultRepository;

    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        $results = $this->resultRepository->findAll();
        $numberOfUsers = count($users);
        $numberOfResults = count($results);

        return $this->render('admin/views/index.html.twig', [
            'numberOfUsers' => $numberOfUsers,
            'numberOfResults' => $numberOfResults
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Easy Sports Tests');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-solid fa-users', User::class);
        yield MenuItem::linkToCrud('Tests', 'fas fa-solid fa-chart-line', Test::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-solid fa-tag', Tag::class);

    }
}