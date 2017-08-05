			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('users');?>"><?php echo $this->lang->line('users_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_user_button')?></li>
			</ul>
			
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

			<div id="perm_err" class="alert alert-danger fade in" style="display: none">
				<label for="permissions[]" class="error"></label>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			</div>
			
			<div class="wrapper wrapper-content animated fadeInRight">
				<?php
				$attributes = array('id' => 'user-form');
				echo form_open(site_url('users/add'), $attributes);
				?>
					<legend><?php echo $this->lang->line('user_info_label')?></legend>
						
					<div class="row">
						<div class="col-sm-6">
								<div class="form-group">
									<label><?php echo $this->lang->line('username_label')?></label>

									<?php echo form_input(array(
										'name' => 'user_name',
										'value' => '',
										'class' => 'form-control',
										'placeholder' => 'Username',
										'id' => 'user_name'
									)); ?>

								</div>
								
								<div class="form-group">
									<label><?php echo $this->lang->line('email_label')?></label>

									<?php echo form_input(array(
										'name' => 'user_email',
										'value' => '',
										'class' => 'form-control',
										'placeholder' => 'Email',
										'id' => 'user_email'
									)); ?>

								</div>
								
								<div class="form-group">
									<label><?php echo $this->lang->line('password_label')?></label>
									<input class="form-control" type="password" placeholder="Password" name='user_password' id='user_password'>
								</div>
											
								<div class="form-group">
									<label><?php echo $this->lang->line('confirm_password_label')?></label>
									<input class="form-control" type="password" placeholder="Confirm Password" name='conf_password' id='conf_password'>
								</div>
								
								<div class="form-group">
									<label><?php echo $this->lang->line('user_role_label')?></label>
									<select class="form-control" name='role_id' id='role_id'>
										<?php
											foreach($this->role->get_all()->result() as $role)
												echo "<option value='".$role->role_id."'>".$role->role_desc."</option>";
										?>
									</select>
								</div>
								<hr>
								<div class="form-group">
									<label><?php echo $this->lang->line('select_shop_label')?></label> <br>
									<select class="form-control" name='shop_id' id='shop_id'>
										<?php
											foreach($this->shop->get_all()->result() as $shop)
												echo "<option value='".$shop->id."'>".$shop->name."</option>";
										?>
									</select>
								</div>
								
								
						</div>
						
						<div class="col-sm-6">
							<div class="form-group">
								<label><?php echo $this->lang->line('allowed_modules_label')?></label>
								<?php
									foreach($this->module->get_all()->result() as $module)
										echo "<label class='checkbox'><input type='checkbox' name='permissions[]' value='".$module->module_id."'>".$module->module_desc."</label><br/>";
								?>
							</div>
						</div>
					</div>
					
					<hr/>
					
					<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
					<a href="<?php echo site_url('users');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
				</form>
			</div>
			<script>
			$(document).ready(function(){
				$('#user-form').validate({
					rules:{
						user_name:{
							required: true,
							minlength: 4,
							remote: '<?php echo site_url("users/exists");?>'
						},
						user_email:{
							required: true,
							email: true
						},
						user_password:{
							required: true,
							minlength: 4
						},
						conf_password:{
							required: true,
							equalTo: '#user_password'
						},
						"permissions[]": { 
							required: true, 
							minlength: 1 
						} 
					},
					messages:{
						user_name:{
							required: "Please fill user name.",
							minlength: "The length of username must be greater than 4",
							remote: "Username is already existed in the system"
						},
						user_email:{
							required: "Please fill email address",
							email: "Please provide valid email address"
						},
						user_password:{
							required: "Please fill user password.",
							minlength: "The length of password must be greater than 4"
						},
						conf_password:{
							required: "Please fill confirm password",
							equalTo: "Password and confirm password do not match."
						},
						"permissions[]": "Please select which modules are able to access."
					},
					errorPlacement: function(error, element) {
						if (element.attr("name") == "permissions[]" ) {
							$("#perm_err label").html($(error).text());
							$("#perm_err").show();
						} else {
							error.insertAfter(element);
						}
					}
				});
			});
		</script>

