<?php declare(strict_types=1);


namespace App\Repository;


use App\Entity\UserSession;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class UserSessionRepository
 * @package App\Repository
 *
 * @method UserSession find($id, $lockMode = null, $lockVersion = null)
 * @method UserSession findOneBy(array $criteria, array $orderBy = null)
 * @method UserSession[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSessionRepository extends EntityRepository
{

    /**
     * @param UserSession $userPendingLogin
     * @return UserSession
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(UserSession $userPendingLogin): UserSession
    {
        if (! $userPendingLogin->getId()) {
            $this->getEntityManager()->persist($userPendingLogin);
        }

        $this->getEntityManager()->flush($userPendingLogin);

        return $userPendingLogin;
    }
}
