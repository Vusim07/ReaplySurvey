<?php
// Load our environment variables from the .env file:
(Dotenv\Dotenv::createImmutable(__DIR__))->load();

class Auth0Auth extends \LimeSurvey\PluginManager\PluginBase
{
    protected $storage = 'DbStorage';

    public function init()
    {
        $this->subscribe('beforeLogin');
        $this->subscribe('newUserSession');
    }

    public function beforeLogin()
    {
        $auth0_domain = $_ENV['AUTH0_DOMAIN'];
        $client_id = $_ENV['AUTH0_CLIENT_ID'];
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/callback';

        $login_url = "https://$auth0_domain/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=openid%20profile%20email";

        header('Location: ' . $login_url);
        exit();
    }

    public function newUserSession()
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $auth0 = new \Auth0\SDK\Auth0([
            'domain' => $_ENV['AUTH0_DOMAIN'],
            'client_id' => $_ENV['AUTH0_CLIENT_ID'],
            'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
            'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . '/callback',
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
