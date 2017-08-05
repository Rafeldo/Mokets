<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Mokets</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url('css/bootstrap.min.css');?>" rel="stylesheet">
    
    <!-- Animation core CSS -->
    <link href="<?php echo base_url('css/animate.css');?>" rel="stylesheet">

    <!-- Custom styles for this template -->
	 <link href="<?php echo base_url('fonts/ptsan/stylesheet.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('css/dashboard.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('css/bootstrap-datetimepicker.min.css');?>" rel="stylesheet">
    
    <!-- Menu CSS -->
    <link href="<?php echo base_url('css/menu/metisMenu.min.css');?>" rel="stylesheet">
    
    <!-- Font CSS -->
    <link href="<?php echo base_url('css/font-awesome/css/font-awesome.min.css');?>" rel="stylesheet">
    
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo base_url('js/jquery.js');?>"></script>
    <script src="<?php echo base_url('js/bootstrap.min.js');?>"></script>
    <script src="<?php echo base_url('js/dashboard.js');?>"></script>
    <script src="<?php echo base_url('js/jquery.validate.js');?>"></script>
    <script src="<?php echo base_url('js/modernizr.custom.js');?>"></script>
    <script src="<?php echo base_url('js/bootstrap-datetimepicker.min.js');?>"></script>
    
    <script src="<?php echo base_url('js/menu/metisMenu.min.js');?>"></script>
    <script src="<?php echo base_url('js/menu/sb-admin-2.js');?>"></script>
     
     <!-- For Location Picker -->
    <script src="<?php echo base_url('js/location/locationpicker.jquery.min.js');?>"></script>
    <script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places&key=
    <?php echo $this->config->item('gmap_api_key');?>'></script>
  </head>

  <body>