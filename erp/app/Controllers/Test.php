<?php
/**
 * PT Indo Ocean - ERP System
 * Test Controller
 */

namespace App\Controllers;

class Test extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * API Integration Test
     */
    public function api()
    {
        $data = [
            'title' => 'API Integration Test',
            'currentPage' => 'test',
        ];

        return $this->view('test/api', $data);
    }
}
