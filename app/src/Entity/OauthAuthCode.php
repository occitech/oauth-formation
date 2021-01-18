<?php

namespace App\Entity;

use App\Repository\OauthAuthCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * @ORM\Entity(repositoryClass=OauthAuthCodeRepository::class)
 */
class OauthAuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $identifier;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expiryDateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $revoked = false;

    /**
     * @ORM\Column(type="text")
     */
    protected $redirectUri;

    /**
     * @ORM\Column(type="json")
     */
    protected $scopes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): self
    {
        $this->revoked = $revoked;

        return $this;
    }
}
