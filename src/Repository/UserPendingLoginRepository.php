<?php declare(strict_types=1);


namespace App\Repository;


use App\Entity\UserMetaInfo;
use App\Entity\UserPendingLogin;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class UserPendingLoginRepository
 * @package App\Repository
 *
 * @method UserPendingLogin find($id, $lockMode = null, $lockVersion = null)
 * @method UserPendingLogin findOneBy(array $criteria, array $orderBy = null)
 * @method UserPendingLogin[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPendingLoginRepository extends EntityRepository
{
    /**
     * @param UserMetaInfo $userMetaInfo
     * @param string $redirectUrl
     * @param string $sessionTransferUrl
     * @return UserPendingLogin
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createPendingLogin(
        UserMetaInfo $userMetaInfo,
        string $redirectUrl,
        string $sessionTransferUrl
    ): UserPendingLogin
    {
        $userPendingLogin = new UserPendingLogin();

        $userPendingLogin->setUserMetaInfo($userMetaInfo);
        $now = new DateTime();
        $userPendingLogin->setCreated($now);
        $userPendingLogin->setLastRefreshTime($now);
        $userPendingLogin->setConfirmed(false);
        $userPendingLogin->setRedirectUrl($redirectUrl);
        $userPendingLogin->setSessionTransferUrl($sessionTransferUrl);
        $userPendingLogin->setGeneratedToken(
            $this->generateFreshToken(
                $userMetaInfo->getMetaHash() . time()
            )
        );

        $this->getEntityManager()->persist($userPendingLogin);
        $this->getEntityManager()->flush($userPendingLogin);

        return $userPendingLogin;
    }

    /**
     * @param UserPendingLogin $userPendingLogin
     * @return UserPendingLogin
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(UserPendingLogin $userPendingLogin): UserPendingLogin
    {
        if (! $userPendingLogin->getId()) {
            $this->getEntityManager()->persist($userPendingLogin);
        }

        $this->getEntityManager()->flush($userPendingLogin);

        return $userPendingLogin;
    }

    /**
     * @param string $salt
     * @return string
     */
    protected function generateFreshToken(string $salt): string
    {
        $token = hash('sha256', $salt);

        if ($this->findOneBy(['generatedToken' => $token])) {
            return $this->generateFreshToken($salt . time());
        }

        return $token;
    }
}
