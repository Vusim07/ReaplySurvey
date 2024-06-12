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
        $this->subscribe('remoteControlLogin');
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

    // Handle Auth0 callback
    public function actionAuth0Callback()
    {
        require_once __DIR__ . '/../../../../vendor/autoload.php';

        $auth0_domain = $_ENV['AUTH0_DOMAIN'];
        $client_id = $_ENV['AUTH0_CLIENT_ID'];
        $client_secret = $_ENV['AUTH0_CLIENT_SECRET'];
        $redirect_uri = $_ENV['AUTH0_REDIRECT_URI'];

        error_log('Auth0 callback initiated');

        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            error_log('Authorization code received: ' . $code);

            // Exchange authorization code for access token
            $token_url = "https://$auth0_domain/oauth/token";
            $response = $this->httpPost($token_url, [
                'grant_type' => 'authorization_code',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'code' => $code,
                'redirect_uri' => $redirect_uri,
            ]);

            $auth0_response = json_decode($response, true);
            if (isset($auth0_response['access_token'])) {
                $access_token = $auth0_response['access_token'];
                error_log('Access token received: ' . $access_token);

                // Retrieve user info
                $user_url = "https://$auth0_domain/userinfo";
                $user_info = $this->httpGet($user_url, [
                    'Authorization: Bearer ' . $access_token
                ]);

                $userInfo = json_decode($user_info, true);
                if ($userInfo) {
                    error_log('User info retrieved: ' . print_r($userInfo, true));
                    $this->handleUserLogin($userInfo);
                    return;
                } else {
                    error_log('Failed to retrieve user info.');
                }
            } else {
                error_log('Failed to exchange authorization code for access token. Response: ' . $response);
            }
        } else {
            error_log('Authorization code not found in the callback request.');
        }

        // Redirect to login page on failure
        Yii::app()->user->setFlash('error', 'Authentication failed.');
        Yii::app()->getController()->redirect(array('/admin/authentication/sa/login'));
    }

    // Handle new user session after Auth0 login
    public function newUserSession()
    {
        require_once __DIR__ . '/../../../../vendor/autoload.php';

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
            error_log('User session created for: ' . print_r($userInfo, true));
            $this->handleUserLogin($userInfo);
        } else {
            error_log('Failed to create user session.');
            Yii::app()->user->setFlash('error', 'Authentication failed.');
            Yii::app()->getController()->redirect(array('/admin/authentication/sa/login'));
        }
    }

    // Handle remote control login event
    public function remoteControlLogin()
    {
        require_once __DIR__ . '/../../../../vendor/autoload.php';

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

            // Generate session key
            $sessionKey = $this->getSessionKey($user);

            return $sessionKey;
        } else {
            // Handle login failure
            return null;
        }
    }

    // Helper function to generate session key
    protected function getSessionKey($user)
    {
        $identity = new UserIdentity($user->users_name, '');
        $identity->authenticate();
        if ($identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 30; // 30 days
            Yii::app()->user->login($identity, $duration);
            return Yii::app()->session->sessionID;
        }
        return null;
    }

    // Helper function to handle user login
    protected function handleUserLogin($userInfo)
    {
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

        error_log('User successfully logged in: ' . $username);

        // Redirect to admin dashboard
        Yii::app()->getController()->redirect(array('/admin'));
    }

    // Helper function for HTTP POST requests
    protected function httpPost($url, $data)
    {
        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    // Helper function for HTTP GET requests
    protected function httpGet($url, $headers = [])
    {
        $options = [
            'http' => [
                'header' => $headers,
                'method' => 'GET',
            ],
        ];
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    // Method to handle custom logout
    public function customLogout()
    {
        Yii::app()->user->logout();
        Yii::app()->getController()->redirect(Yii::app()->homeUrl);
    }
}

