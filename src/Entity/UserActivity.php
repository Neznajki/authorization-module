<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserActivity
 *
 * @ORM\Table(name="user_activity", indexes={@ORM\Index(name="user_session_id", columns={"user_session_id"})})
 * @ORM\Entity
 */
class UserActivity
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
     * @var \DateTime
     *
     * @ORM\Column(name="activity_time", type="datetime", nullable=false)
     */
    private $activityTime;

    /**
     * @var \UserSession
     *
     * @ORM\ManyToOne(targetEntity="UserSession")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_session_id", referencedColumnName="id")
     * })
     */
    private $userSession;


}
