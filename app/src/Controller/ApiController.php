<?php

namespace App\Controller;

use App\Entity\OauthAccessToken;
use App\Entity\User;
use App\Repository\OauthAccessTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\UploadedFileFactory;

class ApiController extends AbstractController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    protected $server;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        /** @var OauthAccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $entityManager->getRepository(OauthAccessToken::class);
        // Init our repositorxies

        // Path to authorization server's public key
        $publicKeyPath = '/var/www/public.key';

        // Setup the authorization server
        $this->server = new ResourceServer(
            $accessTokenRepository,
            $publicKeyPath
        );
    }

    /**
     * @Route("/api/infos", name="private_infos")
     */
    public function index(Request $request): Response
    {
        $psrHttpFactory = new PsrHttpFactory(
            new ServerRequestFactory(),
            new StreamFactory(),
            new UploadedFileFactory(),
            new ResponseFactory()
        );
        $psrRequest = $psrHttpFactory->createRequest($request);
        $psrRequest = $psrRequest->withParsedBody(
            json_decode($request->getContent(), true)
        );

         $this->server->validateAuthenticatedRequest($psrRequest);

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
