<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;



class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private RouterInterface $router;
    private Security $security;

    public function __construct(RouterInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse
    {
        $user = $this->security->getUser();

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return new RedirectResponse($this->router->generate('admin'));
            }
            return new RedirectResponse($this->router->generate('home'));
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }
}