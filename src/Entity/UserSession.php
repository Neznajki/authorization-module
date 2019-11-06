<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserSession
 *
 * @ORM\Table(name="user_session", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="user_meta_info_id", columns={"user_meta_info_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserSessionRepository")
 */
class UserSession
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
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * @return int|null
     */
    public function getId(): ?int
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
}
