<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view($_tpl.'header');
?>
<div id="content" class="float_r faqs">
    <h2><?=$error_title?></h2>
    <?=$error_message?>
</div>
<?php
$this->load->view($_tpl.'footer');
