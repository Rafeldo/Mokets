			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li>
					<a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> 
					<span class="divider"></span>
				</li>
				<li><?php echo $this->lang->line('reviews_list_label')?></li>
			</ul>
			
			<?php
			$attributes = array('class' => 'form-inline','method' => 'POST');
			echo form_open(site_url('reviews/search'), $attributes);
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
						<th><?php echo $this->lang->line('appuser_name_label')?></th>
						<th><?php echo $this->lang->line('date_label')?></th>
						<th><?php echo $this->lang->line('detail_label')?></th>
						
						<?php if(in_array('delete',$allowed_accesses)):?>
						<th><?php echo $this->lang->line('delete_label')?></th>
						<?php endif;?>
					</tr>
					<?php
						if(!$count=$this->uri->segment(3))
							$count = 0;
						if(isset($reviews) && count($reviews->result())>0):
							foreach($reviews->result() as $review):					
					?>
					<tr>
						<td><?php echo ++$count;?></td>
						<td><?php echo $this->item->get_info($review->item_id)->name;?></td>
						<td><?php echo $this->appuser->get_info($review->appuser_id)->username;?></td>
						<td><?php echo $this->common->date_formatting($review->added);?></td>
						<td><a href='<?php echo site_url('reviews/detail/'.$review->id);?>'>Detail</a></td>
						
						<?php if(in_array('delete',$allowed_accesses)):?>
						<td><a href='<?php echo site_url("reviews/delete/".$review->id);?>'><i class='glyphicon glyphicon-trash'></i></a></td>
						<?php endif;?>
					</tr>
						<?php
						endforeach;
						else:
						?>
					<tr>
						<td colspan='7'>
						<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
						<?php echo $this->lang->line('no_reviews_data_message')?>
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
