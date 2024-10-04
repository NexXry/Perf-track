<?php

namespace App\Controller;

use App\Entity\Tracking;
use App\Repository\ExerciceRepository;
use App\Repository\TrackingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExercicesController extends AbstractController
{
    #[Route('/exercices', name: 'app_exercices')]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        $user = $this->getUser();
        $exercices = array_map(function ($exercice) {
            return $exercice->getName();
        }, $user->getExercices()->toArray());

        $trackings = array_map(function ($tracking) {
            return [
                'reps' => $tracking->getReps(),
                'exercice' => $tracking->getExercice()->getName(),
            ];
        }, $user->getTrackings()->toArray());
       
        return $this->render('exercices/index.html.twig', [
            'controller_name' => 'ExercicesController',
            'exercices' => $exercices,
            'trackings' => $trackings
        ]);
    }

    #[Route('/exercices/set', name: 'app_exercices_set_tracking')]
    public function tracking(Request $request, ExerciceRepository $exerciceRepository, TrackingRepository $trackingRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $exerciceName = $request->getPayload()->get('name');
        $count = $request->getPayload()->get('count');
        $today = new \DateTimeImmutable("midnight");

        $exercice = $exerciceRepository->findOneBy(['name' => $exerciceName]);
        $trackingToday = $trackingRepository->findOneBy(["createdAt" => $today, 'Exercice' => $exercice]);

        if (!$trackingToday) {
            $tracking = new Tracking();
            $tracking
                ->setExercice($exercice)
                ->setReps($count)
                ->setCreatedAt($today)
                ->setUser($user);
            $em->persist($tracking);
            $em->flush();

            $user->addTracking($tracking);

            $em->persist($user);
            $em->flush();

        } else {
            $trackingToday->setReps($count);
            $em->persist($trackingToday);
            $em->flush();
        }


        return $this->json([], 200);
    }

    #[Route('/add', name: 'app_exercices_add')]
    public function add(ExerciceRepository $exerciceRepository): Response
    {
        $user = $this->getUser();

        $exercices = array_map(function ($exercice) {
            return $exercice->getName();
        }, $exerciceRepository->findAll());
        $userExercices = array_map(function ($exercice) {
            return $exercice->getName();
        }, $user->getExercices()->toArray());

        return $this->render('exercices/add.html.twig', [
            'controller_name' => 'ExercicesController',
            'exercices' => $exercices,
            'userExercices' => $userExercices
        ]);
    }

    #[Route('/add/exercice', name: 'app_exercices_add_user')]
    public function addToUser(Request $request, ExerciceRepository $exerciceRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $exerciceName = $request->getPayload()->get('name');
        $exercice = $exerciceRepository->findOneBy(['name' => $exerciceName]);

        if ($exercice) {
            $user->addExercices($exercice);
            $em->persist($user);
            $em->flush();
        } else {
            $this->json([], 400);
        }

        return $this->json([], 200);
    }
}
