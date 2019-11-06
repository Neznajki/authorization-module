<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMetaInfo
 *
 * @ORM\Table(name="user_meta_info", uniqueConstraints={@ORM\UniqueConstraint(name="u_meta_hash", columns={"meta_hash"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserMetaInfoRepository")
 */
class UserMetaInfo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_hash", type="string", length=64, nullable=false)
     */
    private $metaHash;

    /**
     * @var string
     *
     * @ORM\Column(name="php_session_id", type="string", length=64, nullable=false)
     */
    private $phpSessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=15, nullable=false)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="text", length=65535, nullable=false)
     */
    private $userAgent;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMetaHash(): string
    {
        return $this->metaHash;
    }

    /**
     * @param string $metaHash
     */
    public function setMetaHash(string $metaHash): void
    {
        $this->metaHash = $metaHash;
    }

    /**
     * @return string
     */
    public function getPhpSessionId(): string
    {
        return $this->phpSessionId;
    }

    /**
     * @param string $phpSessionId
     */
    public function setPhpSessionId(string $phpSessionId): void
    {
        $this->phpSessionId = $phpSessionId;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return bool
     */
    public function isSame(UserMetaInfo $metaInfo): bool
    {
        return $this->getPhpSessionId() === $metaInfo->getPhpSessionId() &&
            $this->getIpAddress() === $metaInfo->getIpAddress() &&
            $this->getUserAgent() === $metaInfo->getUserAgent();
    }
}
