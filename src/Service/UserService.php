<?php declare(strict_types=1);


namespace App\Service;


use App\Entity\User;
use App\Entity\UserPendingLogin;
use App\Entity\UserSession;
use App\Repository\UserPendingLoginRepository;
use App\Repository\UserRepository;
use App\Repository\UserSessionRepository;
use DateTime;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService
{

    /** @var UserRepository */
    protected $userRepository;
    /** @var PasswordEncryptService */
    protected $passwordEncryptService;
    /** @var UserSessionRepository */
    protected $userSessionRepository;
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param PasswordEncryptService $passwordEncryptService
     * @param UserSessionRepository $userSessionRepository
     * @param UserPendingLoginRepository $pendingLoginRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        UserRepository $userRepository,
        PasswordEncryptService $passwordEncryptService,
        UserSessionRepository $userSessionRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->userRepository = $userRepository;
        $this->passwordEncryptService = $passwordEncryptService;
        $this->userSessionRepository = $userSessionRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $userName
     * @return User|null
     */
    public function getUserByUserName(string $userName): ?User
    {
        return $this->userRepository->findOneBy(['login' => $userName]);
    }

    /**
     * @param string $userName
     * @param string $password
     * @param string $repeatPassword
     * @throws Exception
     */
    public function registerUser(string $userName, string $password, string $repeatPassword)
    {
        if (!preg_match('/[a-z][a-z0-9]{3,}/', $userName)) {
            throw new InvalidArgumentException('login should be at least 4 characters long and should begin with letter');
        }

        if (empty($password)) {
            throw new InvalidArgumentException("password should contain at least one symbol");
        }

        if ($password !== $repeatPassword) {
            throw new InvalidArgumentException("password do not match to repeat");
        }

        if ($this->userRepository->findBy(['login' => $userName])) {
            throw new InvalidArgumentException("user already exists please chose new one {$userName}");
        }

        $newUser = new User();

        $newUser->setLogin($userName);
        $encryptedPassword = $this->passwordEncryptService->encodePassword($newUser, $password);
        $newUser->setPassword($encryptedPassword);
        $newUser->setCreated(new DateTime());

        $this->userRepository->save($newUser);
    }

    /**
     * @param UserPendingLogin $pendingLogin
     * @return UserSession
     * @throws Exception
     */
    public function createSession(UserPendingLogin $pendingLogin): UserSession
    {
        $userSession = new UserSession();

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $userSession->setUser($user);
        $userSession->setUserMetaInfo($pendingLogin->getUserMetaInfo());
        $userSession->setIsActive(true);
        $userSession->setCreated(new DateTime());

        $this->userSessionRepository->save($userSession);

        return $userSession;
    }
}
