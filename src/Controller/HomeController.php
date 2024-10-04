<?php

namespace App\Controller;

use App\Repository\TrackingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ChartBuilderInterface $chartBuilder, TrackingRepository $trackingRepository): Response
    {
        $user = $this->getUser();
        $exercices = $user->getExercices();
        $charts = [];

        foreach ($exercices as $exercice) {
            $trackings = $trackingRepository->findTrackingsByExerciceForCurrentWeek($exercice);

            $labels = [];
            $data = [];
            foreach ($trackings as $tracking) {
                $date = $tracking->getCreatedAt()->format('Y-m-d');
                if (!in_array($date, $labels)) {
                    $labels[] = $date;
                }
                $data[] = $tracking->getReps();
            }

            $maxY = max($data) + 5 + $user->getObjective();
            $minY = min($data) - $user->getObjective();

            $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
            $chart->setData([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $exercice->getName(),
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => '#7480ff',
                        'data' => $data,
                        'tension' => 0.8,
                    ],
                ],
            ]);

            $chart->setOptions([
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'min' => $minY,
                        'max' => $maxY,
                        'ticks' => [
                            'stepSize' => 5
                        ]
                    ],
                ],
                'plugins' => [
                    'annotation' => [
                        'annotations' => [
                            'line1' => [
                                'type' => 'line',
                                'yMin' => $user->getObjective(),
                                'yMax' => $user->getObjective(),
                                'borderColor' => 'red',
                                'borderWidth' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

            $charts[] = $chart;
        }

        return $this->render('home/index.html.twig', [
            'charts' => $charts,
        ]);
    }
}
