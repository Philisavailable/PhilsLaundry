<?php

namespace App\Tests\Security;

use App\Security\UsersAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Mockery;

class AuthenticatorTest extends TestCase
{
    public function testAuthenticate()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        /** @var UrlGeneratorInterface|Mockery\MockInterface $urlGenerator */
        $authenticator = new UsersAuthenticator($urlGenerator);

        /** @var Request|Mockery\MockInterface $request */
        $request = Mockery::mock(Request::class);
        /** @var SessionInterface|Mockery\MockInterface $session */
        $session = Mockery::mock(SessionInterface::class);

        $inputBag = new InputBag([
            'email' => 'test@example.com',
            'password' => 'password',
            '_csrf_token' => 'csrf_token',
        ]);

        $request->shouldReceive('getPayload')->andReturn($inputBag);
        $request->shouldReceive('getSession')->andReturn($session);

        $session->shouldReceive('set')
            ->with(SecurityRequestAttributes::LAST_USERNAME, 'test@example.com');

        // Act
        $passport = $authenticator->authenticate($request);

        // Assert
        $this->assertInstanceOf(Passport::class, $passport);
        $this->assertInstanceOf(UserBadge::class, $passport->getBadge(UserBadge::class));
        $this->assertInstanceOf(CsrfTokenBadge::class, $passport->getBadge(CsrfTokenBadge::class));
    }

    public function testOnAuthenticationSuccessWithTargetPath()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        /** @var UrlGeneratorInterface|Mockery\MockInterface $urlGenerator */
        $authenticator = new UsersAuthenticator($urlGenerator);

        /** @var Request|Mockery\MockInterface $request */
        $request = Mockery::mock(Request::class);
        /** @var SessionInterface|Mockery\MockInterface $session */
        $session = Mockery::mock(SessionInterface::class);
        /** @var TokenInterface|Mockery\MockInterface $token */
        $token = Mockery::mock(TokenInterface::class);

        $request->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('get')->with('_security.main.target_path')->andReturn('some_target_path');

        // Act
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        // Assert
        /** @var RedirectResponse $response */
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('some_target_path', $response->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithoutTargetPath()
    {
        // Arrange
        $urlGenerator = Mockery::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')
            ->with('show_dashboard')
            ->andReturn('/');

        /** @var UrlGeneratorInterface|Mockery\MockInterface $urlGenerator */
        $authenticator = new UsersAuthenticator($urlGenerator);

        /** @var Request|Mockery\MockInterface $request */
        $request = Mockery::mock(Request::class);
        /** @var SessionInterface|Mockery\MockInterface $session */
        $session = Mockery::mock(SessionInterface::class);
        /** @var TokenInterface|Mockery\MockInterface $token */
        $token = Mockery::mock(TokenInterface::class);

        $request->shouldReceive('getSession')->andReturn($session);
        $session->shouldReceive('get')->with('_security.main.target_path')->andReturn(null);

        // Act
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        // Assert
        /** @var RedirectResponse $response */
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
