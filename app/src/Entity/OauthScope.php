<?php

namespace App\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class OauthScope implements ScopeEntityInterface
{
    use EntityTrait;

    /**
     * OauthScope constructor.
     * @param $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
