<?php

namespace App\Entity;

use App\Repository\OauthRefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * @ORM\Entity(repositoryClass=OauthRefreshTokenRepository::class)
 */
class OauthRefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait, RefreshTokenTrait;

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
