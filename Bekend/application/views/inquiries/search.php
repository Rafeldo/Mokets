			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li>
					<a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> 
					<span class="divider"></span>
				</li>
				<li>
					<a href="<?php echo site_url('inquiries');?>"><?php echo $this->lang->line('inquiries_list_label')?></a>
					<span class="divider"></span>
				</li>
				<li><?php echo $this->lang->line('search_result_label')?></li>
			</ul>
			
			
			
			<?php
			$attributes = array('class' => 'form-inline','method' => 'POST');
			echo form_open(site_url('inquiries/search'), $attributes);
			?>
				<div class="form-group">

			   		<?php echo form_input(array(
			   			'name' => 'searchterm',
			   			'value' => html_entity_decode( $searchterm ),
			   			'class' => 'form-control',
			   			'placeholder' => 'Search',
			   			'id' => ''
			   		)); ?>
			   		
			  	</div>
			  	<button type="submit" class="btn btn-default"><?php echo $this->lang->line('search_button')?></button>
			  	<a href='<?php echo site_url('inquiries');?>' class="btn btn-default"><?php echo $this->lang->line('reset_button')?></a>
			<?php echo form_close(); ?>
			
			<br/>
			
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
						<th><?php echo $this->lang->line('item_name_label')?></th>
						<th><?php echo $this->lang->line('name_label')?></th>
						<th><?php echo $this->lang->line('date_label')?></th>
						<th><?php echo $this->lang->line('detail_label')?></th>
						<?php if(in_array('delete',$allowed_accesses)):?>
						<th><?php echo $this->lang->line('delete_label')?></th>
						<?php endif;?>
					</tr>
					<?php
						if(!$count=$this->uri->segment(3))
							$count = 0;
						if(isset($inquiries) && count($inquiries->result())>0):
							foreach($inquiries->result() as $inquiry):					
					?>
					<tr>
						<td><?php echo ++$count;?></td>
						<td><?php echo $this->item->get_info($inquiry->item_id)->name;?></td>
						<td><?php echo $inquiry->name;?></td>
						<td><?php echo $inquiry->added;?></td>
						<td><a href='<?php echo site_url('inquiries/detail/'.$inquiry->id);?>'><?php echo $this->lang->line('detail_label')?></a></td>
						
						<?php if(in_array('delete',$allowed_accesses)):?>
						<td><a href='<?php echo site_url("inquiries/delete/".$inquiry->id);?>'><i class='glyphicon glyphicon-trash'></i></a></td>
						<?php endif;?>
					</tr>
						<?php
						endforeach;
						else:
						?>
					<tr>
						<td colspan='7'>
							<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
							<?php echo $this->lang->line('no_inquiries_data_message')?>
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
