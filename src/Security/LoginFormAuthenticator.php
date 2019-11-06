<?php declare(strict_types=1);


namespace App\Security;

use App\Repository\UserRepository;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /** @var Kernel */
    protected $kernel;
    /** @var UserRepository */
    protected $userRepository;
    /** @var RouterInterface */
    protected $router;
    /** @var CsrfTokenManagerInterface */
    protected $csrfTokenManager;
    /** @var UserPasswordEncoderInterface */
    protected $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     * @param Kernel $kernel
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(
        Kernel $kernel,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->kernel           = $kernel;
        $this->router           = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder  = $passwordEncoder;
        $this->userRepository   = $userRepository;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'login'      => $request->request->get('login'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['login']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userRepository->findOneBy(['login' => $credentials['login']]);

        if (! $user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('user could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->getSuccessLoginPage();
    }

    /**
     * Override to control what happens when the user hits a secure page
     * but isn't logged in yet.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     * @throws Exception
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {

        $routeInfo = $this->router->match($request->getPathInfo());
        $routeName = $routeInfo['_route'];

        if (
            'app_login' === $routeName ||
            'app_register' === $routeName
        ) {
            unset($routeInfo['_route']);
            $subRequest          = $request->duplicate([], null, $routeInfo);

            return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }

        return new RedirectResponse($this->getLoginUrl());
    }

    /**
     * @return RedirectResponse
     */
    public function getSuccessLoginPage(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('index'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
