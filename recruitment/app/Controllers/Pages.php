<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Pages Controller - Static/Information Pages
 */
class Pages extends BaseController {
    
    public function about() {
        $this->view('pages/about', [
            'pageTitle' => 'About Us'
        ]);
    }
    
    public function contact() {
        $this->view('pages/contact', [
            'pageTitle' => 'Contact Us'
        ]);
    }
}
