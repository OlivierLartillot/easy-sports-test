<?php

namespace App\Controller;

use App\Repository\ResultRepository;
use App\Repository\TestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class PlayerController extends AbstractController
{
    /**
     * @Route("/player", name="app_player")
     */
    public function index(): Response
    {
        return $this->render('common/home.html.twig', [
            'controller_name' => 'CommonController',
        ]);
    }

    /**
     * @Route("{slug}/chart/{id}", name="app_chart")
     */
    public function chart(ChartBuilderInterface $chartBuilder, Security $security, $id, UserRepository $userRepository, ResultRepository $resultRepository, TestRepository $testRepository, $slug): Response
    {
        //id du test
        $testId = $id;
        // récupère le test pour le name par exemple, description, photo etc
        if($slug != null){
            $userSlug = $slug;
            $user = $userRepository->findBy(['slug'=>$userSlug]);
        }else{
            $user = $security->getUser();
        }
        
        $test = $testRepository->find($id);
        //user courant
        // $user = $security->getUser();
        $myResults = $resultRepository->findBy([
            'test'=>$testId,
            'user' => $user,
        ]);

        $resultOfficialsData = [];
        $resultOfficialsDate = [];
        $resultTrainingsData = [];
        $resultTrainingsDate = [];

        foreach ($myResults as $result) {
       
            if ($result->getStatus() == 1) {
                $resultOfficialsDate [] = $result->getDoneAt()->format('m-Y'); 
                $resultOfficialsData [] = $result->getResult();
            }
            else {
                $resultTrainingsDate [] = $result->getDoneAt()->format('m-Y'); 
                $resultTrainingsData [] = $result->getResult();
            }
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData(
            [
                'labels' => $resultOfficialsDate,
                'datasets' => [
                    [
                        'label' => 'officiel',
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'data' => $resultOfficialsData,
                    ], 
                ] 
            
            ],   
    
    );


        $chart2 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart2->setData([
            'labels' => $resultTrainingsDate,
            'datasets' => [
                [
                    'label' => 'Entrainement',
                    'backgroundColor' => 'rgb(0, 99, 132)',
                    'borderColor' => 'rgb(0, 99, 132)',
                    'data' => $resultTrainingsData,
                ],
                  /*               [
                    'label' => 'entrainement',
                    'backgroundColor' => 'rgb(0, 99, 132)',
                    'borderColor' => 'rgb(0, 99, 132)',
                    'data' => ['52','45'],
                ], */
            ],
        ]);

        $chart->setOptions([/* ... */]);

            

        return $this->render('common/chart.html.twig', [
            'test' => $test,
            'chart' => $chart,
            'chart2' => $chart2,
            'user' => $user, 
            'myresults' => $myResults 
  
        ]);
    }
}


