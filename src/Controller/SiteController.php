<?php declare(strict_types=1);


namespace App\Controller;


use App\Entity\UserPendingLogin;
use App\Repository\UserPendingLoginRepository;
use App\Service\UserService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /** @var UserPendingLoginRepository */
    protected $pendingLoginRepository;
    /** @var UserService */
    protected $userService;

    /**
     * SiteController constructor.
     * @param UserPendingLoginRepository $pendingLoginRepository
     * @param UserService $userService
     */
    public function __construct(
        UserPendingLoginRepository $pendingLoginRepository,
        UserService $userService
    ) {
        $this->pendingLoginRepository = $pendingLoginRepository;
        $this->userService = $userService;
    }

    /**
     * @Route("/", name="index")
     * @throws Exception
     */
    public function indexAction()
    {
        if (array_key_exists(AuthorizationController::PRIVATE_AUTH_TOKEN, $_SESSION)) {
            $generatedToken = $_SESSION[AuthorizationController::PRIVATE_AUTH_TOKEN];
            $pendingData    = $this->pendingLoginRepository->findOneBy(['generatedToken' => $generatedToken]);

            if ($pendingData && ! $pendingData->isConfirmed()) {
                return $this->confirmLogin($pendingData);
            }
        }
        $this->get('security.token_storage')->getToken()->getUser();

        return new Response('thanks for using our service !!');
    }

    /**
     * @param UserPendingLogin $pendingData
     * @return RedirectResponse|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    protected function confirmLogin(UserPendingLogin $pendingData)
    {
        $pendingData->setUserSession($this->userService->createSession($pendingData));
        $this->pendingLoginRepository->save($pendingData);

        $fullUrl = sprintf(
            '%s/%s/%s',
            rtrim($pendingData->getSessionTransferUrl(), '/'),
            $pendingData->getId(),
            $pendingData->getUserMetaInfo()->getPhpSessionId()
        );
        $curl    = curl_init($fullUrl);

        curl_setopt_array(
            $curl,
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => 0,
            ]
        );

        $response = curl_exec($curl);

        if ($response !== 'OK') {
            return new Response("invalid response from external service");
        }

        $pendingData->setConfirmed(true);
        $this->pendingLoginRepository->save($pendingData);

        return new RedirectResponse($pendingData->getRedirectUrl());
    }
}
