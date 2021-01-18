<?php

namespace App\Repository;

use App\Entity\OauthAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 * @method OauthAccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthAccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthAccessToken[]    findAll()
 * @method OauthAccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthAccessTokenRepository extends ServiceEntityRepository implements
    AccessTokenRepositoryInterface
{
    protected $entityManger;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthAccessToken::class);
    }

    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): OauthAccessToken {
        $accessToken = new OauthAccessToken();
        $accessToken->setUserIdentifier($userIdentifier);
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        return $accessToken;
    }

    public function persistNewAccessToken(
        AccessTokenEntityInterface $accessTokenEntity
    ) {
        $accessTokenEntity->setRevoked(false);
        $this->_em->persist($accessTokenEntity);
        $this->_em->flush();
    }

    public function revokeAccessToken($tokenId)
    {
        $accessToken = $this->findOneBy(["identifier" => $tokenId]);
        $accessToken->setRevoked(true);

        $this->_em->flush();
    }

    public function isAccessTokenRevoked($tokenId): ?bool
    {
        return $this->findOneBy(["identifier" => $tokenId])->getRevoked();
    }
}
