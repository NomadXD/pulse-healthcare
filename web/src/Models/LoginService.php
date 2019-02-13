<?php

namespace Pulse\Models;

use DB;
use Pulse\Models\User\Credentials;
use Pulse\Models\User\Session;

define('SECONDS_PER_DAY', 86400);
define('COOKIE_VALID_PERIOD_DAYS', 7);
define('SESSION_USER', 'SESSION_USER');
define('SESSION_KEY', 'SESSION_KEY');

class LoginService implements BaseModel
{
    public static $testFlag = false;
    private static $testCookie = array();

    /**
     * @return Session|null
     * @throws \Pulse\Exceptions\UserNotExistException
     */
    public static function continueSession(): ?Session
    {
        if (LoginService::sessionCookiesExist()) {
            $sessionUser = LoginService::getSessionUser();
            $sessionKey = LoginService::getSessionKey();

            $session = Session::resumeSession($sessionUser, $sessionKey);

            if ($session == null) {
                // Session key invalid: delete cookie
                LoginService::deleteCookie();
                return null;
            } else {
                // Session key valid: LOGIN
                return $session;
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $userId
     * @param string $password
     * @return Session|null
     * @throws \Pulse\Exceptions\UserNotExistException
     */
    public function logInSession(string $userId, string $password): ?Session
    {
        // Delete previous cookies
        LoginService::deleteCookie();

        // Verify correct password and authenticate
        $credentials = Credentials::fromExistingCredentials($userId, $password);
        if ($credentials == null) {
            // Invalid credentials
            return null;
        }

        $session = Session::createSession($userId);
        LoginService::saveCookie($session);
        return $session;
    }


    /**
     * @param string $userId
     * @param string $password
     * @return Session|null
     * @throws \Pulse\Exceptions\UserAlreadyExistsException
     * @throws \Pulse\Exceptions\UserNotExistException
     */
    public static function signInSession(string $userId, string $password): ?Session
    {
        // Delete previous cookies
        LoginService::deleteCookie();

        // Verify correct password and authenticate
        Credentials::fromNewCredentials($userId, $password);

        $session = Session::createSession($userId);
        LoginService::saveCookie($session);
        return $session;
    }

    /**
     * @return string
     */
    private static function getSessionUser(): ?string
    {
        if (!isset(LoginService::getCookieVariable()[SESSION_USER])) {
            return null;
        }
        return LoginService::getCookieVariable()[SESSION_USER];
    }

    /**
     * @return string
     */
    private static function getSessionKey(): ?string
    {
        if (!isset(LoginService::getCookieVariable()[SESSION_KEY])) {
            return null;
        }
        return LoginService::getCookieVariable()[SESSION_KEY];
    }

    /**
     * @param Session $session
     */
    private static function saveCookie(Session $session)
    {
        LoginService::setCookie(SESSION_USER, $session->getSessionUserId());
        LoginService::setCookie(SESSION_KEY, $session->getSessionKey());
    }

    /**
     * @return bool
     */
    private static function sessionCookiesExist(): bool
    {
        return isset(LoginService::getCookieVariable()[SESSION_KEY]) and isset(LoginService::getCookieVariable()[SESSION_USER]);
    }

    private static function deleteCookie()
    {
        DB::delete('sessions', 'user=%s', LoginService::getSessionUser());
    }

    public static function getCookieVariable(): array
    {
        if (LoginService::$testFlag) {
            return LoginService::$testCookie;
        } else {
            return $_COOKIE;
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    private static function setCookie(string $name, string $value)
    {
        if (LoginService::$testFlag) {
            LoginService::$testCookie[$name] = $value;
        } else {
            setcookie($name, $value, time() + (SECONDS_PER_DAY * COOKIE_VALID_PERIOD_DAYS), "/");
        }
    }
}