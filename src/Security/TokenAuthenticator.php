<?php
/**
 * @file
 * Token authentication using adgangsplatform introspection end-point.
 */

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TokenAuthenticator.
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $client;

    private $clientId;
    private $clientSecret;
    private $endPoint;

    /**
     * TokenAuthenticator constructor.
     *
     * @param ParameterBagInterface $params
     * @param HttpClientInterface $httpClient
     */
    public function __construct(ParameterBagInterface $params, HttpClientInterface $httpClient)
    {
        $this->client = $httpClient;

        $this->clientId = $params->get('openPlatform.id');
        $this->clientSecret = $params->get('openPlatform.secret');
        $this->endPoint = $params->get('openPlatform.introspection.url');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return $request->headers->has('authorization');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('authorization');
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        // Parse token information from the bearer authorization header.
        preg_match('/Bearer\s(\w+)/', $credentials, $matches);
        if (2 !== count($matches)) {
            return null;
        }

        $token = $matches[1];

        try {
            $response = $this->client->request('POST', $this->endPoint.'?access_token='.$token, [
                'auth_basic' => [$this->clientId, $this->clientSecret],
            ]);

            if (200 !== $response->getStatusCode()) {
                return null;
            } else {
                $content = $response->getContent();
                $data = json_decode($content);

                // Token not valid, hence not active at the introspection end-point.
                if (true == !$data->active) {
                    return null;
                }
            }
        } catch (HttpExceptionInterface $e) {
            return null;
        } catch (ExceptionInterface $e) {
            return null;
        }

        // Create user object.
        $user = new User();
        $user->setPassword($token);
        $user->setExpires(new \DateTime($data->expires, new \DateTimeZone('Europe/Copenhagen')));
        $user->setAgency($data->agency);
        $user->setAuthType($data->type);
        $user->setClientId($data->clientId);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // In case of an token, no credential check is needed.
        // Return `true` to cause authentication success
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => 'Authentication failed',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}