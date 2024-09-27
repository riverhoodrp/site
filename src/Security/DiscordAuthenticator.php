<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

final class DiscordAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository
    ) {}

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate("auth_discord_start"), Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get("_route") === "auth_discord_login";
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient("discord");
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var DiscordResourceOwner $discordUser */
                $discordUser = $client->fetchUserFromToken($accessToken);

                $user = $this->userRepository->findOneBy(["discordId" => $discordUser->getId()]);

                $avatar = "https://cdn.discordapp.com/avatars/" . $discordUser->getId() . "/" . $discordUser->getAvatarHash() . ".png";

                if (null === $user) {
                    $user = new User();
                    $user->setDiscordId($discordUser->getId());
                    $user->setAvatarHash($avatar);
                    $this->em->persist($user);
                } else {
                    if ($user->getAvatarHash() !== $avatar) {
                        $user->setAvatarHash($avatar);
                    }
                }

                $this->em->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate("app_home"), Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate("auth_discord_start"), Response::HTTP_TEMPORARY_REDIRECT);
    }
}