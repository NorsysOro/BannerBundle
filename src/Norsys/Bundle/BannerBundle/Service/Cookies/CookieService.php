<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Service\Cookies;

use Symfony\Component\HttpFoundation\RequestStack;

class CookieService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function getCookieFromRequest(string $cookieName): ?string
    {
        return $this->requestStack->getCurrentRequest()?->cookies->get($cookieName);
    }
}
