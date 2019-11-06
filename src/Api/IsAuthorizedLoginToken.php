<?php declare(strict_types=1);


namespace App\Api;


use App\Repository\UserMetaInfoRepository;
use App\Repository\UserPendingLoginRepository;
use App\Service\ServerHelperService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use JsonRpcServerBundle\Contract\MethodHandlerInterface;
use JsonRpcServerBundle\Exception\RpcMessageException;

class IsAuthorizedLoginToken implements MethodHandlerInterface
{

    /** @var UserPendingLoginRepository */
    protected $pendingLoginRepository;
    /** @var UserMetaInfoRepository */
    protected $metaInfoRepository;
    /** @var ServerHelperService */
    protected $serverHelperService;

    /**
     * IsAuthorizedLoginToken constructor.
     * @param UserPendingLoginRepository $pendingLoginRepository
     * @param UserMetaInfoRepository $metaInfoRepository
     * @param ServerHelperService $serverHelperService
     */
    public function __construct(
        UserPendingLoginRepository $pendingLoginRepository,
        UserMetaInfoRepository $metaInfoRepository,
        ServerHelperService $serverHelperService
    )
    {
        $this->pendingLoginRepository = $pendingLoginRepository;
        $this->metaInfoRepository     = $metaInfoRepository;
        $this->serverHelperService = $serverHelperService;
    }

    /**
     * NOTICE all arguments are optional with default value definition, use getRequiredParameters to specify them
     *
     * @param string $token
     * @param string $sessionId
     * @param array $serverParams
     * @return mixed json serializable data
     * @throws RpcMessageException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handle(
        string $token = '',
        string $sessionId = '',
        array $serverParams = []
    ): array {
        $pendingLogin = $this->pendingLoginRepository->findOneBy(['generatedToken' => $token]);
        $expectingMetaData = $this->metaInfoRepository->getOrCreateUserMetaInfo(
            $sessionId,
            $this->serverHelperService->getIpAddress($serverParams),
            $this->serverHelperService->getUserAgent($serverParams)
        );

        if (! $pendingLogin->getUserMetaInfo()->isSame($expectingMetaData)) {
            throw new RpcMessageException('user session data changed session could be expired');
        }

        $userSession = $pendingLogin->getUserSession();
        if (! $userSession) {
            throw new RpcMessageException('user session not found');
        }

        if (! $userSession->isActive()) {
            throw new RpcMessageException('user session is not active');
        }

        //could be some expire validation

        $pendingLogin->setLastRefreshTime(new DateTime());
        $this->pendingLoginRepository->save($pendingLogin);

        return ['success' => 1,'user' => $pendingLogin->getUserSession()->getUser(), "tokenExpiresIn" => 9999999 ];
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return 'isAuthorizedLoginToken';
    }

    /**
     * @return array
     */
    public function getRequiredParameters(): array
    {
        return [
            'token',
            'sessionId',
            'serverParams',
        ];
    }
}
