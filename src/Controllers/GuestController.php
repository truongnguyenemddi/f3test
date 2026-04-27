<?php
namespace App\Controllers;

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
        if ($this->app->exists('SESSION.user')) {
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
        $this->app->set('page_title', 'Login account');
        $this->pageContent = 'pages/login.php';
        echo \View::instance()->render($this->pageContent);
    }
}