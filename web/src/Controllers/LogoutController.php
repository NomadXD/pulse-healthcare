<?php declare(strict_types=1);

namespace Pulse\Controllers;

use Pulse\Models\AccountSession\LoginService;

class LogoutController extends BaseController
{
    public function post()
    {
        $redirect = $this->httpHandler()->postParameter('redirect');
        if ($redirect == null) {
            $redirect = "http://$_SERVER[HTTP_HOST]";
        }
        LoginService::signOutSession();
        $this->httpHandler()->redirect($redirect);
    }
}