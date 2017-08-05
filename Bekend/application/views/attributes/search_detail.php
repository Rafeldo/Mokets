			<?php
				$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li>
					<a href="<?php echo site_url() . "/dashboard";?>">
					<?php echo $this->lang->line('dashboard_label')?>
					</a> 
					<span class="divider"></span>
				</li>
				<li>
					<a href="<?php echo site_url() . "/attributes";?>">
					<?php echo $this->lang->line('att_list_label')?>
					</a> 
					<span class="divider"></span>
				</li>
				<li><?php echo $this->lang->line('att_detail_list_label')?></li>
			</ul>
			
			<div class='row'>
				<div class='col-sm-9'>
					<?php
					$attributes = array('class' => 'form-inline');
					echo form_open(site_url('attributes/search_detail/' . $attribute_header_id), $attributes);
					?>
						<div class="form-group">

							<?php echo form_input(array(
								'name' => 'searchterm',
								'value' => html_entity_decode( $searchterm ),
								'class' => 'form-control',
								'placeholder' => 'Search'
							)); ?>

					  	</div>
					  	<button type="submit" class="btn btn-default"><?php echo $this->lang->line('search_button')?></button>
					  	<a href='<?php echo site_url('attributes/view_detail/' . $attribute_header_id);?>' class="btn btn-default"><?php echo $this->lang->line('reset_button')?></a>
					  	<a href="<?php echo site_url('attributes');?>" class="btn btn-default"><?php echo $this->lang->line('back_button')?></a>
					</form>
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
					<th><?php echo $this->lang->line('att_detail_name_label')?></th>
					<th><?php echo $this->lang->line('att_name_label')?></th>
					<?php 
						if(!$this->session->userdata('is_shop_admin')) {
							if(in_array('edit',$allowed_accesses)):
					?>
						<th><?php echo $this->lang->line('edit_label')?></th>
					<?php endif; } else {?>
						<th><?php echo $this->lang->line('edit_label')?></th>
					<?php } ?>
					
					<?php 
						if(!$this->session->userdata('is_shop_admin')) {
							if(in_array('delete',$allowed_accesses)):?>
						<th><?php echo $this->lang->line('delete_label')?></th>
					<?php endif; } else {?>
						<th><?php echo $this->lang->line('delete_label')?></th>
					<?php } ?>
					
				</tr>
				<?php
										
					if(!$count=$this->uri->segment(3))
						$count = 0;
					if(isset($attributes_detail) && count($attributes_detail->result())>0):
						foreach($attributes_detail->result() as $attribute):					
				?>
						<tr>
							<td><?php echo ++$count;?></td>
							<td><?php echo $attribute->name;?></td>
							<td><?php echo $this->attribute_header->get_info($attribute->id)->name;?></td>
							<?php 
								if(!$this->session->userdata('is_shop_admin')) {
									if(in_array('edit',$allowed_accesses)):
							?>
							
									<td><a href='<?php echo site_url("attributes/edit_detail/".$attribute->id);?>'><i class='glyphicon glyphicon-edit'></i></a></td>
							<?php endif; } else {?>
									<td><a href='<?php echo site_url("attributes/edit_detail/".$attribute->id);?>'><i class='glyphicon glyphicon-edit'></i></a></td>
									
							<?php } ?>
							<?php 
								if(!$this->session->userdata('is_shop_admin')) {
									if(in_array('delete',$allowed_accesses)): 
							?>
									<td><a href='<?php echo site_url("attributes/delete_detail/".$attribute->id);?>'><i class='glyphicon glyphicon-trash'></i></a></td>
							<?php endif; } else {?>
									<td><a href='<?php echo site_url("attributes/delete_detail/".$attribute->id);?>'><i class='glyphicon glyphicon-trash'></i></a></td>
							<?php } ?>
							
						</tr>
						<?php
						endforeach;
					else:
				?>
						<tr>
							<td colspan='7'>
							<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
							<?php echo $this->lang->line('no_att_detail_data_message')?>
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

			
