<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserPendingLogin
 *
 * @ORM\Table(name="user_pending_login", indexes={@ORM\Index(name="user_meta_info_id", columns={"user_meta_info_id"}), @ORM\Index(name="user_session_id", columns={"user_session_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserPendingLoginRepository")
 */
class UserPendingLogin
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
     * @ORM\Column(name="generated_token", type="string", length=64, nullable=false)
     */
    private $generatedToken;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", length=128, nullable=false)
     */
    private $redirectUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="session_transfer_url", type="string", length=128, nullable=false)
     */
    private $sessionTransferUrl;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_refresh_time", type="datetime", nullable=false)
     */
    private $lastRefreshTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed;

    /**
     * @var UserMetaInfo
     *
     * @ORM\ManyToOne(targetEntity="UserMetaInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_meta_info_id", referencedColumnName="id")
     * })
     */
    private $userMetaInfo;

    /**
     * @var UserSession
     *
     * @ORM\ManyToOne(targetEntity="UserSession")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_session_id", referencedColumnName="id")
     * })
     */
    private $userSession;

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
    public function getGeneratedToken(): string
    {
        return $this->generatedToken;
    }

    /**
     * @param string $generatedToken
     */
    public function setGeneratedToken(string $generatedToken): void
    {
        $this->generatedToken = $generatedToken;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return string
     */
    public function getSessionTransferUrl(): string
    {
        return $this->sessionTransferUrl;
    }

    /**
     * @param string $sessionTransferUrl
     */
    public function setSessionTransferUrl(string $sessionTransferUrl): void
    {
        $this->sessionTransferUrl = $sessionTransferUrl;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getLastRefreshTime(): DateTime
    {
        return $this->lastRefreshTime;
    }

    /**
     * @param DateTime $lastRefreshTime
     */
    public function setLastRefreshTime(DateTime $lastRefreshTime): void
    {
        $this->lastRefreshTime = $lastRefreshTime;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return UserMetaInfo
     */
    public function getUserMetaInfo(): UserMetaInfo
    {
        return $this->userMetaInfo;
    }

    /**
     * @param UserMetaInfo $userMetaInfo
     */
    public function setUserMetaInfo(UserMetaInfo $userMetaInfo): void
    {
        $this->userMetaInfo = $userMetaInfo;
    }

    /**
     * @return UserSession
     */
    public function getUserSession(): UserSession
    {
        return $this->userSession;
    }

    /**
     * @param UserSession $userSession
     */
    public function setUserSession(UserSession $userSession): void
    {
        $this->userSession = $userSession;
    }
}
