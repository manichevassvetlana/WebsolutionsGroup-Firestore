<?php

namespace WebsolutionsGroup\Auth;

use App\Users;
use Illuminate\Support\Facades\Facade;
use WebsolutionsGroup\Auth\Middleware\Authenticate;
use Lcobucci\JWT\Parser;

class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes()
    {
        static::$app->make('router')->auth();
    }

    public static function guest()
    {
        return !Auth::check();
    }

    public static function check()
    {
        if (!isset($_SESSION)) session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            if(Auth::checkByToken($_SESSION['access_token'])) return true;
            return false;
        } else return false;
    }

    public static function user()
    {
        if (!isset($_SESSION)) session_start();
        if(!Auth::check()) return null;
        $token = (new Parser())->parse((string) $_SESSION['access_token']);
        $uid = $token->getClaim('user_id');
        return Users::find($uid);
    }

    public static function checkByToken($token)
    {
        if (!isset($_SESSION)) session_start();
        $token = (new Parser())->parse((string) $token); // Parses from a string
        $uid = $token->getClaim('user_id');
        $email = $token->getClaim('email');

        $user = Users::find($uid);
        return $user && $user->email == $email;
    }
}
