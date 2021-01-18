<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @Route("/api/infos", name="private_infos")
     */
    public function index(): Response
    {
        $user = $this->userRepository->find(1);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse(
            [
                "data" => $user->getPrivateInfo(),
            ],
            200
        );
    }
}
