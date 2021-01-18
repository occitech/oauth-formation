<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements
    ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getClientEntity($clientIdentifier)
    {
        return $this->findOneBy(["client_id" => $clientIdentifier]);
    }

    public function validateClient(
        $clientIdentifier,
        $clientSecret,
        $grantType
    ): bool {
        $client = $this->getClientEntity($clientIdentifier);

        if (!$client) {
            return false;
        }

        return $client->getSecret() &&
            !hash_equals($client->getSecret(), (string) $clientSecret);
    }
}
