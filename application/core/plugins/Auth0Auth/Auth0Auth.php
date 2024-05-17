<?php

use Dotenv\Dotenv;


class Auth0Auth extends \LimeSurvey\PluginManager\PluginBase
{
    protected $storage = 'DbStorage';

    public function init()
    {
        $this->loadEnv(); // Load .env variables
        $this->subscribe('beforeLogin');
        $this->subscribe('newUserSession');
    }

    // Load .env variables
    protected function loadEnv()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 4));
        $dotenv->load();
    }

    // Redirect to Auth0 login page
    public function beforeLogin()
    {
        $auth0_domain = $_ENV['AUTH0_DOMAIN'];
        $client_id = $_ENV['AUTH0_CLIENT_ID'];
        $redirect_uri = $_ENV['AUTH0_REDIRECT_URI'];
        $login_url = "https://$auth0_domain/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=openid%20profile%20email";

        header('Location: ' . $login_url);
        exit();
    }

    // Create new user session
    public function newUserSession()
    {
        require_once __DIR__ . '..\..\..\..\vendor\autoload.php';

        $auth0_domain = $_ENV['AUTH0_DOMAIN'];
        $client_id = $_ENV['AUTH0_CLIENT_ID'];
        $client_secret = $_ENV['AUTH0_CLIENT_SECRET'];
        $redirect_uri = $_ENV['AUTH0_REDIRECT_URI'];

        $auth0 = new \Auth0\SDK\Auth0([
            'domain' => $auth0_domain,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
        ]);

        $userInfo = $auth0->getUser();

        if ($userInfo) {
            $username = $userInfo['email']; // Or another unique identifier from user info

            // Create or find the user in LimeSurvey
            $user = User::model()->findByAttributes(['users_name' => $username]);
            if (!$user) {
                // Create new user if doesn't exist
                $user = new User();
                $user->users_name = $username;
                $user->password = hash('sha256', uniqid()); // Set a random password
                $user->save();
            }

            // Log in the user
            Yii::app()->user->setId($user->uid);
            Yii::app()->user->setName($user->users_name);

            // Redirect to home or dashboard
            Yii::app()->getController()->redirect(array('/admin'));
        } else {
            // Handle login failure
            Yii::app()->user->setFlash('error', 'Authentication failed.');
            Yii::app()->getController()->redirect(array('/admin/authentication/sa/login'));
        }
    }
}
