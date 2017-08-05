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
						<li><?php echo $this->lang->line('follows_list_label')?></li>
					</ul>
				</div>
			</div>
			
			<table class="table table-striped table-bordered">
				<tr>
					<th><?php echo $this->lang->line('no_label')?></th>
					<th><?php echo $this->lang->line('shop_name_label')?></th>
					<th><?php echo $this->lang->line('appuser_name_label')?></th>
					<th><?php echo $this->lang->line('date_label')?></th>
				</tr>
				<?php
					if(!$count=$this->uri->segment(3))
						$count = 0;
					if(isset($follows) && count($follows->result())>0):
						foreach($follows->result() as $follow):					
				?>
				<tr>
					<td><?php echo ++$count;?></td>
					<td><?php echo $this->shop->get_info($follow->shop_id)->name;?></td>
					<td><?php echo $this->appuser->get_info($follow->appuser_id)->username;?></td>
					<td><?php echo $follow->added;?></td>
				</tr>
				<?php
					endforeach;
					else:
				?>
				<tr>
					<td colspan='5'><?php echo $this->lang->line('no_follows_data_message')?></td>
				</tr>
				<?php
				endif;
				?>
			</table>
			<?php 
			$this->pagination->initialize($pag);
			echo $this->pagination->create_links();
			?>
