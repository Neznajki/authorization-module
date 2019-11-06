<?php declare(strict_types=1);


namespace App\Repository;


use App\Entity\UserMetaInfo;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RuntimeException;

/**
 * Class UserMetaInfoRepository
 * @package App\Repository
 * @method UserMetaInfo findOneBy(array $criteria, array $orderBy = null)
 */
class UserMetaInfoRepository extends EntityRepository
{
    /**
     * @param string $phpSession
     * @param string $ipAddress
     * @param string $userAgent
     * @return UserMetaInfo
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getOrCreateUserMetaInfo(string $phpSession, string $ipAddress, string  $userAgent): UserMetaInfo
    {
        $metaInfo = new UserMetaInfo();

        $metaInfo->setPhpSessionId($phpSession);
        $metaInfo->setIpAddress($ipAddress);
        $metaInfo->setUserAgent($userAgent);
        $metaInfo->setMetaHash(hash('sha256', $phpSession . $ipAddress . $userAgent));

        $savedMetaInfo = $this->findOneBy([
            'phpSessionId' => $phpSession,
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
        ]);

        if ($savedMetaInfo) {
            if (! $savedMetaInfo->isEqualTo($metaInfo)) {
                //TODO to reduce this exception we can make table with custom hash generated. it won't have much records, so table scan on large fields won't deliver performance issues
                throw new RuntimeException('duplicate hash detected');
            }

            return $savedMetaInfo;
        }

        $this->getEntityManager()->persist($metaInfo);
        $this->getEntityManager()->flush($metaInfo);

        return $metaInfo;
    }
}
