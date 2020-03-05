<?php

namespace App\Controller;

use App\Core\Controller;
use App\Core\View;
use App\Core\Settings;

/**
 * Security controller
 */
class Auth extends Controller
{
    /* private $settings;

    public function __construct(Settings $settings)
    {
        parent::__construct();
        $this->settings = $settings;
    }*/
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        // Make sure an admin user is logged in for example
        // return false;
    }

    public function loginAction()
    {
        $messages = [];
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $username && $password){

            $settings = new Settings();
            $config = $settings->getConfig();

            if ($config['admin_username'] == $username && $config['admin_password'] == $password){
                $_SESSION['is_admin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;

                header('Location: /admin/dashboard/index');
            }else{
                $messages[] = 'Login failed: Invalid username or password';
            }
        }
        View::renderTemplate('auth/index.twig',[
            'messages' => $messages
        ]);
    }
    public function logoutAction()
    {
        session_unset($_SESSION['is_admin']);
        session_unset($_SESSION['username']);
        session_unset($_SESSION['password']);
        session_destroy();
        header('Location: /');
    }
}