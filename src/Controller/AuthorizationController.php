<?php declare(strict_types=1);


namespace App\Controller;


use App\Exception\ValidateException;
use App\Security\LoginFormAuthenticator;
use App\Service\UserService;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthorizationController extends AbstractController
{
    const PRIVATE_AUTH_TOKEN = 'private_auth_token';
    /** @var UserService */
    protected $userService;
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;
    /** @var LoginFormAuthenticator */
    protected $loginFormAuthenticator;

    /**
     * AuthorizationController constructor.
     * @param UserService $userService
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param LoginFormAuthenticator $loginFormAuthenticator
     */
    public function __construct(
        UserService $userService,
        AuthorizationCheckerInterface $authorizationChecker,
        LoginFormAuthenticator $loginFormAuthenticator
    ) {
        $this->userService            = $userService;
        $this->authorizationChecker   = $authorizationChecker;
        $this->loginFormAuthenticator = $loginFormAuthenticator;
    }

    /**
     * @Route("/login/{token}", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param string|null $token
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, string $token = null): Response
    {

        if ($token) {
            $_SESSION[self::PRIVATE_AUTH_TOKEN] = $token;
        }

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            return $this->loginFormAuthenticator->getSuccessLoginPage();
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function register(Request $request): Response
    {
        $errorData = [];
        $userName  = '';

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            return $this->loginFormAuthenticator->getSuccessLoginPage();
        }

        if ($request->getRealMethod() === 'POST') {
            $userName = $request->get('login', '');

            try {
                $this->userService->registerUser(
                    $userName,
                    $request->get('password', ''),
                    $request->get('passwordRepeat', '')
                );
                //        <label for="inputPassword" class="sr-only">Password</label>
                //        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
                //        <label for="inputPassword" class="sr-only">Repeat Password</label>
                //        <input type="password" name="passwordRepeat" id="inputPasswordRepeat" class="form-control" placeholder="Repeat password" required>

                return new RedirectResponse('/login');
            } catch (InvalidArgumentException $exception) {
                $errorData['messageData'] = $exception->getMessage();
                $errorData['messageKey']  = $exception->getCode();
            }
        }


        return $this->render(
            'security/register.html.twig',
            [
                'last_username' => $userName,
                'error'         => $errorData,
            ]
        );
    }

    /**
     * @Route("/validate/register/{loginEmail}/{password}/{passwordRepeat}", name="app_validate_register")
     *
     * @param string $loginEmail
     * @param string $password
     * @param string $passwordRepeat
     * @return JsonResponse
     * @throws ValidateException
     */
    public function validateRegisterUser(
        string $loginEmail,
        /** @noinspection PhpUnusedParameterInspection */
        string $password = '',
        /** @noinspection PhpUnusedParameterInspection */
        string $passwordRepeat = ''
    ): JsonResponse {
        if (! preg_match('/[a-z0-9.]+@[a-z0-9]+\\.[a-z]+/i', $loginEmail)) {
            throw new ValidateException('email should be valid (example: example@domain.lv)');
        }

        if ($this->userService->getUserByUserName($loginEmail)) {
            throw new ValidateException(sprintf('email %s already in use', $loginEmail));
        }

        //could be additional email validation like checking if on domain is SMTP server, asking smpt server about do he know him and something like that (DLC is for additional price)

        return new JsonResponse(['success' => 1]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     * @throws Exception
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new Exception('Don\'t forget to activate logout in security.yaml');
    }
}
