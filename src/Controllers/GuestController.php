<?php
namespace App\Controllers;

class GuestController {
    protected $app;
    protected $pageContent;
    protected $layout;

    public function __construct() {
        $this->app = \Base::instance();
        $this->layout = 'layouts/public-layout.php';
    }

    /**
     * Auth middleware in
     */
    public function beforeroute() {
        if ($this->app->exists('SESSION.user')) {
            $this->app->reroute('/');
            exit;
        }
    }

    /**
     * layout middleware out
     */
    public function afterroute() {
        if ($this->pageContent) {
            $this->app->set('content', $this->pageContent);
            echo \View::instance()->render($this->layout);
        }
    }

    // Render login
    public function renderLogin() {
        $this->app->set('page_title', 'Login account');
        $this->pageContent = 'pages/login.php';
        $this->layout = 'layouts/auth-layout.php';
    }
}