<?php
namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
/**
 * @var RequestStack
 */
private $requestStack;

/**
 * @param RequestStack $requestStack
 */
public function __construct(RequestStack $requestStack, private UserRepository $userRepository)
{
    $this->requestStack = $requestStack;
}

/**
 * @param JWTCreatedEvent $event
 *
 * @return void
 */
public function onJWTCreated(JWTCreatedEvent $event)
{
    $request = $this->requestStack->getCurrentRequest();

    $payload       = $event->getData();
    $email = $event->getUser()->getUserIdentifier();

    $user = $this->userRepository->findByEmail($email);

    $payload['specialization'] = $user->getSpecialization()?->getName();
    $payload['firstName'] = $user->getFirstName();
    $payload['lastName'] = $user->getLastName();

    $event->setData($payload);

    $header        = $event->getHeader();
    $header['cty'] = 'JWT';

    $event->setHeader($header);
}
}