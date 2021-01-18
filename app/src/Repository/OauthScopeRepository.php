<?php

namespace App\Repository;

use App\Entity\OauthScope;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class OauthScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier): OauthScope
    {
        return new OauthScope($identifier);
    }

    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        return $scopes;
    }
}
