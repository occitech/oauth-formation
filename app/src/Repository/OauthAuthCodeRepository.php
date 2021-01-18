<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\OauthAuthCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

/**
 * @method OauthAuthCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthAuthCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthAuthCode[]    findAll()
 * @method OauthAuthCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthAuthCodeRepository extends ServiceEntityRepository implements
    AuthCodeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthAuthCode::class);
    }

    public function getNewAuthCode(): OauthAuthCode
    {
        return new OauthAuthCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCodeEntity->setRevoked(false);

        $clientRepo = $this->_em->getRepository(Client::class);
        $client = $clientRepo->getClientEntity(
            $authCodeEntity->getClient()->getIdentifier()
        );

        if ($client) {
            $authCodeEntity->setClient($client);
        }

        $this->_em->persist($client);
        $this->_em->persist($authCodeEntity);
        $this->_em->flush();
    }

    public function revokeAuthCode($codeId)
    {
        $authCode = $this->findOneBy(["identifier" => $codeId]);
        $authCode->setRevoked(true);

        $this->_em->flush();
    }

    public function isAuthCodeRevoked($codeId): ?bool
    {
        return $this->findOneBy(["identifier" => $codeId])->getRevoked();
    }
}
