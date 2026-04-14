<?php
namespace App\Controllers;

class AdminController {
    protected $app;
    protected $pageContent;

    public function __construct() {
        $this->app = \Base::instance();
    }

    /**
     * Auth middleware in
     */
    public function beforeroute() {
        if (!$this->app->exists('SESSION.user')) {
            /* 
                // Open this code to enable auth check
                $this->app->reroute('/login');
                exit;
            */
        }
    }

    /**
     * layout middleware out
     */
    public function afterroute() {
        if ($this->pageContent) {
            $this->app->set('content', $this->pageContent);
            echo \View::instance()->render('layouts/admin-layout.php');
        }
    }

    // Render home
    public function renderHome() {
        $this->app->set('page_title', 'Home title');
        $this->pageContent = 'pages/home.php';
    }
}