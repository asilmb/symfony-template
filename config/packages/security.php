<?php
declare(strict_types=1);

use App\Access\Entity\User;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->passwordHasher(User::class)->algorithm('auto');

    $security->provider('app_user_provider')
        ->entity()
        ->class(User::class)
        ->property('email');

    $security->firewall('login')
        ->pattern('^/api/login')
        ->stateless(true)
        ->provider('app_user_provider')
        ->jsonLogin()
        ->checkPath('/api/login')
        ->usernamePath('email')
        ->passwordPath('password')
        ->successHandler('lexik_jwt_authentication.handler.authentication_success')
        ->failureHandler('lexik_jwt_authentication.handler.authentication_failure');

    $security->firewall('api', [
        'pattern' => '^/api',
        'stateless' => true,
        'jwt' => [],
        'provider' => 'app_user_provider',
    ]);

    $security->accessControl()->path('^/api/login')->roles(['PUBLIC_ACCESS']);
    $security->accessControl()->path('^/api')->roles(['IS_AUTHENTICATED_FULLY']);
};