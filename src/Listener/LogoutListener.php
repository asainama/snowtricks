<?php

namespace App\Listener;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class LogoutListener
{
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $event): void
    {
        $this->flashBag->add('success', 'Vous êtes maintenant déconnecté');
    }
}
