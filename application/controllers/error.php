<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Error extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }
    public function _404()
    {
        redirect('front/error/_404');
    }
}