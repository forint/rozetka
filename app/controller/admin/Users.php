<?php

namespace App\Controller\Admin;

use App\Core\Controller;
/**
 * User admin controller
 */
class Users extends Controller
{

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

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
{
    echo 'User admin index';
}
}