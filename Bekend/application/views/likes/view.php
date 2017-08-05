			<?php
			$this->lang->load('ps', 'english');
			?>
			<div class='row'>
				<div class='col-sm-12'>
					<ul class="breadcrumb">
						<li>
							<a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> 
							<span class="divider"></span>
						</li>
						<li><?php echo $this->lang->line('likes_list_label')?></li>
					</ul>
				</div>
			</div>
			<div class="wrapper wrapper-content animated fadeInRight">
			<table class="table table-striped table-bordered">
				<tr>
					<th><?php echo $this->lang->line('no_label')?></th>
					<th><?php echo $this->lang->line('item_name_label')?></th>
					<th><?php echo $this->lang->line('appuser_name_label')?></th>
					<th><?php echo $this->lang->line('date_label')?></th>
				</tr>
				<?php
					if(!$count=$this->uri->segment(3))
						$count = 0;
					if(isset($likes) && count($likes->result())>0):
						foreach($likes->result() as $like):					
				?>
				<tr>
					<td><?php echo ++$count;?></td>
					<td><?php echo $this->item->get_info($like->item_id)->name;?></td>
					<td><?php echo $this->appuser->get_info($like->appuser_id)->username;?></td>
					<td><?php echo $this->common->date_formatting($like->added);?></td>
				</tr>
				<?php
					endforeach;
					else:
				?>
				<tr>
					<td colspan='5'>
					<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
					<?php echo $this->lang->line('no_likes_data_message')?>
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
