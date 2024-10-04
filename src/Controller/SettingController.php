<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingController extends AbstractController
{
    #[Route('/setting', name: 'app_setting')]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        $user = $this->getUser();
        $exercices = array_map(function ($exercice) {
            return $exercice->getName();
        }, $exerciceRepository->findAll());

        return $this->render('setting/index.html.twig', [
            'controller_name' => 'SettingController',
            'objective' => $user->getObjective(),
            'exercices' => $exercices
        ]);
    }

    #[Route('/setting/add', name: 'app_setting_add')]
    public function addExerciceToDatabase(Request $request, ExerciceRepository $exerciceRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }

            return new JsonResponse($errors, 400);
        }

        $exercice->setName($form->get('name')->getData());
        $exist = $exerciceRepository->findOneBy(['name' => $exercice->getName()]);

        if (!$exist) {
            $em->persist($exercice);
            $em->flush();
        }

        return $this->json([], 201);
    }

    #[Route('/setting/remove', name: 'app_setting_remove')]
    public function removeExerciceToDatabase(Request $request, ExerciceRepository $exerciceRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $name = $request->getPayload()->get('name');
        $exist = $exerciceRepository->findOneBy(['name' => $name]);

        if ($exist) {
            $em->remove($exist);
            $em->flush();
        } else {
            return $this->json([], 400);
        }

        return $this->json([], 201);
    }

    #[Route('/setting/set/objective', name: 'app_setting_objective')]
    public function setObjective(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $objective = $request->getPayload()->get('count');

        if (!$objective) {
            return $this->json([], 400);
        }

        $user->setObjective(intval($objective));
        $em->persist($user);
        $em->flush();
        
        return $this->json([], 201);
    }
}
