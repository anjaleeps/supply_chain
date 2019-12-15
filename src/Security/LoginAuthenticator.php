<?php

namespace App\Security;

use App\Entity\DriverAssistant;
use App\Entity\Driver;
use App\Entity\Manager;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return ('app_login_driver_assistant' === $request->attributes->get('_route')
            || 'app_login_driver'=== $request->attributes->get('_route')
            || 'app_login_manager' === $request->attributes->get('_route')
            || 'app_login_customer' === $request->attributes->get('_route'))
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {

        $user_type = $this->getUserType($request);

        $credentials = [
            'user_type' => $user_type,
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user_type = $credentials['user_type'];

        if ($user_type == 1){
            $user = $this->entityManager->getRepository(Manager::class)->findOneBy(['email' => $credentials['email']]);
        }
        elseif ($user_type == 2){
            $user = $this->entityManager->getRepository(Driver::class)->findOneBy(['email' => $credentials['email']]);
        }
        elseif ($user_type == 3){
            $user = $this->entityManager->getRepository(DriverAssistant::class)->findOneBy(['email' => $credentials['email']]);
        }
        
        elseif ($user_type == 4){
            $user = $this->entityManager->getRepository(Customer::class)->findOneBy(['email' => $credentials['email']]);
        }

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function getUserType(Request $request){
        $path = \strtolower($request->getRequestUri());

        if (strstr($path, 'manager')){
            $user_type = 1;
        }
        elseif (strstr($path, 'driver')){
            $user_type = 2;
        }
        elseif (strstr($path, 'driver_assistant')){
            $user_type = 3;
        }
        else{
            $user_type = 4;
        }
        return $user_type;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login_customer');
    }
}
