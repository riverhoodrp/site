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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

final class DiscordAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        HttpClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

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
                    $user->setUsername($discordUser->getUsername());
                    $this->em->persist($user);
                } else {
                    if ($user->getAvatarHash() !== $avatar) {
                        $user->setAvatarHash($avatar);
                    }

                    if ($user->getUsername() !== $discordUser->getUsername()) {
                        $user->setUsername($discordUser->getUsername());
                    }
                }

                $this->em->flush();

                dump("Token 1 " . $accessToken);

                $this->updateUserRolesFromDiscord($accessToken, $user);

                return $user;
            })
        );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function updateUserRolesFromDiscord($accessToken, User $user): void
    {
        $client = $this->clientRegistry->getClient("discord");

        $rolesResponse = $this->httpClient->request('GET', "https://discord.com/api/users/@me/guilds/1184125456853762149/member", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken->getToken(),
            ],
        ]);

        $guildMember = $rolesResponse->toArray();

        error_log("Guild Member Response: " . json_encode($guildMember));

        $roles = $guildMember['roles'] ?? [];

        $this->processUserRoles($user, $roles);
    }

    private function processUserRoles(User $user, array $roles): void
    {
        error_log("Processing roles: " . implode(", ", $roles));

        if (in_array("1259634180615176344", $roles)) {
            $user->addRole('ROLE_ADMIN');
            error_log("ROLE_ADMIN added to user: " . $user->getDiscordId());
        } else {
            error_log("ROLE_ADMIN not added. Role ID did not match.");
        }

        $this->em->flush();
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