<?php

namespace App\Repository;

use App\Entity\OauthRefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * @method OauthRefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthRefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthRefreshToken[]    findAll()
 * @method OauthRefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthRefreshTokenRepository extends ServiceEntityRepository implements
    RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthRefreshToken::class);
    }

    public function getNewRefreshToken()
    {
        return new OauthRefreshToken();
    }

    public function persistNewRefreshToken(
        RefreshTokenEntityInterface $refreshTokenEntity
    ) {
        $refreshTokenEntity->setRevoked(false);
        $this->_em->persist($refreshTokenEntity);
        $this->_em->flush();
    }

    public function revokeRefreshToken($tokenId)
    {
        $refreshToken = $this->findOneBy(["identifier" => $tokenId]);
        $refreshToken->setRevoked(true);

        $this->_em->flush();
    }

    public function isRefreshTokenRevoked($tokenId): ?bool
    {
        return $this->findOneBy(["identifier" => $tokenId])->getRevoked();
    }
}
