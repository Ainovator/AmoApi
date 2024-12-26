<?php

namespace App\Controllers;

use App\Core\View;

class LeadController {
    public function index():void
    {
        View::render("lead/index", [
        'cssFile' => '/css/lead/lead.css?v=' . filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/lead/lead.css')
        ]);
    }
}