<?php
namespace App\Controllers\web;

class GuestController
{
    protected $app;
    protected $pageContent;

    public function __construct()
    {
        $this->app = \Base::instance();
    }

    /**
     * Auth middleware in
     */
    public function beforeroute()
    {
        if ($this->app->exists('SESSION.userID')) {
            $this->app->reroute('/');
            exit;
        }
    }

    /**
     * layout middleware out
     */
    // public function afterroute()
    // {
    //     if ($this->pageContent) {
    //         $this->app->set('content', $this->pageContent);
    //         echo \View::instance()->render('layouts/public-layout.php');
    //     }
    // }

    // Render login
    public function renderLogin()
    {
        echo \View::instance()->render('pages/login.php');
    }

    // Render login
    public function renderLoginJWT() {
        $this->app->set('page_title', 'Login JWT');
        $this->app->set('content', 'pages/login-jwt.php');
        echo \View::instance()->render('layouts/public-layout.php');
    }
}