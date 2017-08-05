<?php $this->load->view('templates/mokets/header');?>

<?php 
	if($edit_mode){
		$data['mode'] = $edit_mode;
		//echo " >>>here " . $edit_mode;
		$this->load->view('templates/mokets/nav',$data);
	} else {
		$this->load->view('templates/mokets/nav');
	}

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar teamps-sidebar-open">
			<?php
				if($sidebar){
					/*
					$shop_id = $this->session->userdata('shop_id');
					if (isset($shop_id) && trim($shop_id) != "") {
							$this->load->view('templates/mokets/sidebar');
					}
					*/
					$this->load->view('templates/mokets/sidebar');
				}
			?>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-offset-2 main teamps-sidebar-push col-md-10">
		
		<?php print_r($content[content]);?>
		
		</div>
	</div>
</div>

<?php $this->load->view('templates/mokets/footer');?>