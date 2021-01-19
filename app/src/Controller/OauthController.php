<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\OauthAccessToken;
use App\Entity\OauthAuthCode;
use App\Entity\OauthRefreshToken;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\OauthAccessTokenRepository;
use App\Repository\OauthAuthCodeRepository;
use App\Repository\OauthRefreshTokenRepository;
use App\Repository\OauthScopeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\UploadedFileFactory;

class OauthController extends AbstractController
{
    protected $entityManager;
    protected $passwordEncoder;
    protected $server;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        /** @var ClientRepository $clientRepository */
        $clientRepository = $entityManager->getRepository(Client::class); // instance of ClientRepositoryInterface
        $scopeRepository = new OauthScopeRepository(); // instance of ScopeRepositoryInterface
        /** @var OauthAccessTokenRepository $accessTokenRepository */
        $accessTokenRepository = $entityManager->getRepository(
            OauthAccessToken::class
        ); // instance of AccessTokenRepositoryInterface
        /** @var OauthAuthCodeRepository $authCodeRepository */
        $authCodeRepository = $entityManager->getRepository(
            OauthAuthCode::class
        ); // instance of AuthCodeRepositoryInterface
        /** @var OauthRefreshTokenRepository $refreshTokenRepository */
        $refreshTokenRepository = $entityManager->getRepository(
            OauthRefreshToken::class
        ); // instance of RefreshTokenRepositoryInterface

        $privateKey = "/var/www/private.key";
        $encryptionKey = "lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen"; // generate using base64_encode(random_bytes(32))

        // Setup the authorization server
        $this->server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $grant = new AuthCodeGrant(
            $authCodeRepository,
            $refreshTokenRepository,
            new \DateInterval("PT10M") // authorization codes will expire after 10 minutes
        );

        $grant->setRefreshTokenTTL(new \DateInterval("P1M")); // refresh tokens will expire after 1 month

        // Enable the authentication code grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval("PT1H") // access tokens will expire after 1 hour
        );
    }

    /**
     * @Route("/authorize", name="oauth_authorize")
     * @param Request $request
     * @return Response
     * @throws OAuthServerException
     */
    public function authorizeAction(Request $request): Response
    {
        // login in form
        if ($request->isMethod("POST")) {
            return $this->approve($request);
        }

        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );
        $psrRequest = $psrHttpFactory->createRequest($request);

        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $this->server->validateAuthorizationRequest($psrRequest);

        // The auth request object can be serialized and saved into a user's session.
        $request->getSession()->set("authRequest", $authRequest);

        // You will probably want to redirect the user at this point to a login endpoint.
        return $this->render("security/login.html.twig");
    }

    /**
     * @Route("/access_token", name="oauth_access_token")
     * @param Request $request
     * @return ResponseInterface
     * @throws OAuthServerException
     */
    public function accessTokenAction(Request $request): ResponseInterface
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

        // Try to respond to the request
        return $this->server->respondToAccessTokenRequest(
            $psrRequest,
            new \Nyholm\Psr7\Response()
        );
    }

    protected function approve(Request $request): Response
    {
        /** @var AuthorizationRequest $authRequest */
        $authRequest = $request->getSession()->get("authRequest");

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            "email" => $request->get("email"),
        ]);

        if (
            !$user ||
            !$this->passwordEncoder->isPasswordValid(
                $user,
                $request->get("password")
            )
        ) {
            throw new NotFoundHttpException();
        }

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser($user); // an instance of UserEntityInterface

        // At this point you should redirect the user to an authorization page.
        // This form will ask the user to approve the client and the scopes requested.

        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $authRequest->setAuthorizationApproved(true);

        $psrResponse = $this->server->completeAuthorizationRequest(
            $authRequest,
            new \Nyholm\Psr7\Response()
        );

        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createResponse($psrResponse);
    }
}
