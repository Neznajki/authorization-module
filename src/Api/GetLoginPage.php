<?php declare(strict_types=1);


namespace App\Api;


use App\Repository\UserMetaInfoRepository;
use App\Repository\UserPendingLoginRepository;
use App\Service\RegistryService;
use App\Service\ServerHelperService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use JsonRpcServerBundle\Contract\MethodHandlerInterface;

class GetLoginPage implements MethodHandlerInterface
{
    /** @var UserMetaInfoRepository */
    protected $metaInfoRepository;
    /** @var UserPendingLoginRepository */
    protected $pendingLoginRepository;
    /** @var RegistryService */
    protected $registryService;
    /** @var ServerHelperService */
    protected $serverHelperService;

    public function __construct(
        UserMetaInfoRepository $metaInfoRepository,
        UserPendingLoginRepository $pendingLoginRepository,
        RegistryService $registryService,
        ServerHelperService $serverHelperService
    ) {
        $this->metaInfoRepository     = $metaInfoRepository;
        $this->pendingLoginRepository = $pendingLoginRepository;
        $this->registryService        = $registryService;
        $this->serverHelperService    = $serverHelperService;
    }

    /**
     * @param string $loginPageType
     * @param string $redirectPage
     * @param string $sessionTransferUrl
     * @param string $sessionId
     * @param array $serverParams
     * @return array json serializable data
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handle(
        string $loginPageType = 'default',
        string $redirectPage = '',
        string $sessionTransferUrl = '',
        string $sessionId = '',
        array $serverParams = []
    ): array {
        $metaInfo = $this->metaInfoRepository->getOrCreateUserMetaInfo(
            $sessionId,
            $this->serverHelperService->getIpAddress($serverParams),
            $this->serverHelperService->getUserAgent($serverParams)
        );

        $pendingLogin = $this->pendingLoginRepository->createPendingLogin(
            $metaInfo,
            $redirectPage,
            $sessionTransferUrl
        );

        return [
            'success' => 1,
            'url'     => $this->registryService->getServerGuiUri() . '/login/' . $pendingLogin->getGeneratedToken(),
        ];
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return 'getLoginPage';
    }

    /**
     * @return array
     */
    public function getRequiredParameters(): array
    {
        return [
            'loginPageType',
            'redirectPage',
            'sessionTransferUrl',
            'sessionId',
            'serverParams',
        ];
        // TODO: Implement getRequiredParameters() method.
    }
}
