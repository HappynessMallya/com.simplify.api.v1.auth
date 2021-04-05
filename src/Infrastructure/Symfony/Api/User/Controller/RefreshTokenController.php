<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Controller;

use App\Infrastructure\Symfony\Api\BaseController;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RefreshTokenController
 * @package App\Infrastructure\Symfony\Api\User\Controller
 */
class RefreshTokenController extends BaseController
{
    /**
     * @Route(path="/token_refresh", methods={"POST"})
     *
     * @param Request $request
     * @return JWTAuthenticationSuccessResponse
     */
    public function refreshTokenAction(Request $request)
    {
        $user = $this->getUser();
        $jwtToken = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);
        $response = new JWTAuthenticationSuccessResponse($jwtToken);

        $event = new AuthenticationSuccessEvent(['token' => $jwtToken], $user, $response);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;
    }
}
