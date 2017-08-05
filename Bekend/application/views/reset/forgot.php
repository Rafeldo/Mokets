<?php
$this->lang->load('ps', 'english');
?>
<?php $this->load->view('templates/mokets/header');?>
	<div class='fluid-container'>
		<div class='row'>
			<div class='col-sm-4 col-sm-offset-3'>
	     		<?php
	     		$attributes = array('id' => 'login-form');
	     		echo form_open(site_url('forgot'), $attributes);
	     		?>
					<h2><?php echo $this->lang->line('forgot_password_label')?></h2>
					<hr/>
					
					<!-- Message -->
					<?php if($this->session->flashdata('success')): ?>
						<div class="alert alert-success fade in">
							<?php echo $this->session->flashdata('success');?>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						</div>
					<?php elseif($this->session->flashdata('error')):?>
						<div class="alert alert-danger fade in">
							<?php echo $this->session->flashdata('error');?>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						</div>
					<?php endif;?>
					
					<div class="form-group">
						<label><?php echo $this->lang->line('email_label')?></label>
						<input class="form-control" type="email" id="email" placeholder="email" name='user_email'>
					</div>
					
					<button class="btn btn-primary" type="submit"><?php echo $this->lang->line('save_button')?></button>
				</form>
			</div>
		</div>
	</div>
	
	<script>
		$(document).ready(function(){
			$('#login-form').validate({
				rules:{
					email:{
						required: true,
						email: true
					}
				},
				messages:{
					email:{
						required: "Please fill email address.",
						email: "Please provide valid email address."
					}
				}
			});
		});
	</script>
	
<?php $this->load->view('partial/footer');?>