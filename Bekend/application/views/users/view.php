			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('users_list_label')?></li>
			</ul>
			
			<div class='row'>
				<div class='col-sm-9'>
					<?php
					$attributes = array('class' => 'form-inline');
					echo form_open(site_url('users/search'), $attributes);
					?>
						<div class="form-group">
							
							<?php echo form_input(array(
								'name' => 'searchterm',
								'value' => '',
								'class' => 'form-control',
								'placeholder' => $this->lang->line('search_message'),
								'id' => ''
							)); ?>

					  	</div>
					  	<button type="submit" class="btn btn-default"><?php echo $this->lang->line('search_button')?></button>
					</form>
				</div>	
				<div class='col-sm-3'>
					<a href='<?php echo site_url('users/add');?>' class='btn btn-primary pull-right'><span class='glyphicon glyphicon-plus'></span> 
					<?php echo $this->lang->line('add_new_user_button')?></a>
				</div>
			</div>
			
			<br/>
			
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
			<div class="wrapper wrapper-content animated fadeInRight">
				<table class="table table-striped table-bordered">
					<tr>
						<th><?php echo $this->lang->line('no_label')?></th>
						<th><?php echo $this->lang->line('username_label')?></th>
						<th><?php echo $this->lang->line('email_label')?></th>
						<th><?php echo $this->lang->line('user_role_label')?></th>
						<?php if(in_array('edit',$allowed_accesses)):?>
							<th><?php echo $this->lang->line('edit_label')?></th>
						<?php endif;?>
						
						
						
					</tr>
					<?php
						if(!$count=$this->uri->segment(3))
							$count = 0;
						if(isset($users) && count($users->result())>0):
							foreach($users->result() as $user):					
					?>
							<tr>
								<td><?php echo ++$count;?></td>
								<td><?php echo $user->user_name;?></td>
								<td><?php echo $user->user_email;?></td>
								<td><?php echo $this->role->get_name($user->role_id);?></td>
								
								<?php if(in_array('edit',$allowed_accesses)):?>
									<td><a href='<?php echo site_url("users/edit/".$user->user_id);?>'><i class='glyphicon glyphicon-edit'></i></a></td>
								<?php endif;?>
								
								
							</tr>
							<?php
							endforeach;
						else:
					?>
							<tr>
								<td colspan='7'>
								<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
								<?php echo $this->lang->line('no_user_data_message')?>
								</td>
							</tr>
					<?php
						endif;
					?>
				</table>
			</div>
			<?php 
				$this->pagination->initialize($pag);
				echo $this->pagination->create_links();
			?>

