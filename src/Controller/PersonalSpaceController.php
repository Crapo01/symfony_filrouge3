<?php

namespace App\Controller;

use App\Entity\Month;
use App\Repository\MonthRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class PersonalSpaceController extends AbstractController
{
    #[Route('/api/user/profile', name: 'profile', methods: ['GET'])]
    public function getProfile(ProfileRepository $profileRepository, SerializerInterface $serializer): Response
    {
        $current_user = $this->getUser();

        $id = $current_user->getId();
        

        $profile = $profileRepository->findOneBy(array('userid' => $id));
        $jsonProfile = $serializer->serialize($profile,'json',['groups'=> 'show']);
        
        return new JsonResponse($jsonProfile,Response::HTTP_OK,[],true);
    }

    #[Route('/api/user/history', name: 'history', methods: ['GET'])]
    public function getHistory(MonthRepository $monthRepository, SerializerInterface $serializer): Response
    {
        $current_user = $this->getUser();

        $id = $current_user->getId();
        

        $history = $monthRepository->findBy(array('userid' => $id));
        $jsonHistory = $serializer->serialize($history,'json',['groups'=> 'show']);
        
        return new JsonResponse($jsonHistory,Response::HTTP_OK,[],true);
    }

    #[Route('/api/user/submit', name: 'submit', methods: ['POST'])]
    public function submitLastMonthAmount(Request $request, MonthRepository $monthRepository, EntityManagerInterface $em, Security $security): JsonResponse
    {
        // Get the current authenticated user
        $user = $security->getUser();
        
        if (!$user) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No authenticated user found.'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Get the user ID from the authenticated user
        $userId = $user->getId();

        // Get the current date
        $currentDate = new \DateTime();

        // Get the first day of the last month
        $lastMonthStart = (clone $currentDate)->modify('first day of last month');

        // Get the last day of the last month
        $lastMonthEnd = (clone $currentDate)->modify('last day of last month');

        // Parse the JSON payload to get the amount
        $data = json_decode($request->getContent(), true);

        // Ensure the "amount" is provided and is a valid number
        $amount = isset($data['amount']) ? $data['amount'] : null;

        if ($amount === null || !is_numeric($amount)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid amount provided.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Find the existing entry for the last month
        $lastMonthRecord = $monthRepository->createQueryBuilder('m')
            ->andWhere('m.date >= :startDate')
            ->andWhere('m.date <= :endDate')
            ->setParameter('startDate', $lastMonthStart->format('Y-m-d'))
            ->setParameter('endDate', $lastMonthEnd->format('Y-m-d'))
            ->andWhere('m.userid = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('m.date', 'DESC') // Order by most recent date
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // If no record exists for the last month, create a new record
        if (!$lastMonthRecord) {
            $newMonth = new Month();
            $newMonth->setUserid($user);
            $newMonth->setDate($lastMonthStart); // Set the date for the first day of last month
            $newMonth->setAmount($amount);

            $em->persist($newMonth);
            $em->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'New record created and amount submitted for the last month.',
                'amount' => $amount
            ]);
        }

        // If the record exists and amount is null, update it
        if ($lastMonthRecord->getAmount() === null) {
            $lastMonthRecord->setAmount($amount);
            $em->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Amount submitted for the last month.',
                'amount' => $amount
            ]);
        }

        // If the amount is already set, return a success message
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Amount has already been submitted for the last month.',
            'amount' => $lastMonthRecord->getAmount()
        ]);
    } 
}
