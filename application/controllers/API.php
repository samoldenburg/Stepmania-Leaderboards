<?php
/**
 * Charts view Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->layout_view = "layout";
        $this->data['title'] = "Stepmania Leaderboards";
    }

    public function index() {
        $this->content_view = "api/index";
    }
}
