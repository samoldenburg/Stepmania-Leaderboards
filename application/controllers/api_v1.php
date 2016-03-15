<?php
/**
 * Charts view Controller
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_v1 extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->layout_view = "ajax";
        $this->data['title'] = "Stepmania Leaderboards";
		$this->output->set_content_type('application/json');
        error_reporting(0);
    }

    public function index() {
        $this->content_view = "api/json";
        $this->data['result'] = array(
            'test',
            'test2',
            'test3'
        );
    }

    public function parse() {
        $this->content_view = "api/json";
        $file = $this->input->post('file');
        $rate = doubleval($this->input->post('rate'));
        $verbose = boolval($this->input->post('verbose'));

        if (!$rate)
            $rate = 1.0;

        if (!$file)
            $this->data['result'] = array('error' => 'No file was sent.');
        elseif ($rate < 0.5 || $rate > 2.0)
            $this->data['result'] = array('error' => 'Invalid rate provided');
        else {
            $difficulty = $this->_process_everything($file, 1.0, null, true);
            $this->data['result'] = array(
                'file_difficulty' => $difficulty,
                'meta' => $this->data['meta']
            );

            if ($verbose) {
                $this->data['result'] = array_merge($this->data['result'], array(
                        'column_distributions' => $this->data['column_distributions_auto'],
                        'formatted' => $this->data['filled_distances']
                    )
                );
            }
        }
    }

    public function parse_meta() {
        $this->content_view = "api/json";
        $file = $this->input->post('file');

        if (!$file)
            $this->data['result'] = array('error' => 'No file was sent.');
        else {
            $difficulty = $this->_process_everything($file, 1.0, null, true, true);
            $this->data['result'] = $this->data['meta'];
        }
    }
}
