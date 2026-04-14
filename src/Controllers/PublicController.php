<?php
namespace App\Controllers;

class PublicController {
    protected $app;
    protected $pageContent;

    public function __construct() {
        $this->app = \Base::instance();
    }

    /**
     * layout middleware out
     */
    public function afterroute() {
        if ($this->pageContent) {
            $this->app->set('content', $this->pageContent);
            echo \View::instance()->render('layouts/public-layout.php');
        }
    }

    // Render about
    public function renderAbout() {
        $this->app->set('page_title', 'About');
        $this->pageContent = 'pages/about.php';
    }

    // Render contact
    public function renderContact() {
        $this->app->set('page_title', 'Contact');
        $this->pageContent = 'pages/contact.php';
    }
}