<?php
$config['template'] = "mokets";

/* pagination */
$config['pagination'] = array();

$config['pagination']['per_page'] = 20;
$config['pagination']['num_links'] = 5;
$config['pagination']['full_tag_open'] =  '<ul class="pagination">';
$config['pagination']['full_tag_close'] = '</ul>';
$config['pagination']['num_tag_open'] = '<li>';
$config['pagination']['num_tag_close'] = '</li>';
$config['pagination']['first_link'] = '&laquo;';
$config['pagination']['first_tag_open'] = '<li>';
$config['pagination']['first_tag_close'] = '</li>';
$config['pagination']['last_link'] = '&raquo;';
$config['pagination']['last_tag_open'] = '<li>';
$config['pagination']['last_tag_close'] = '</li>';
$config['pagination']['next_link'] = '&raquo;';
$config['pagination']['next_tag_open'] = '<li>';
$config['pagination']['next_tag_close'] = '</li>';
$config['pagination']['prev_link'] = '&laquo;';
$config['pagination']['prev_tag_open'] = '<li>';
$config['pagination']['prev_tag_close'] = '</li>';
$config['pagination']['cur_tag_open'] = '<li><a class="active">';
$config['pagination']['cur_tag_close'] = '</a></li>';
$config['pagination']['uri_segment'] = 3;