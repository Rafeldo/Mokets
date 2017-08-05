			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li>
					<a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> 
					<span class="divider"></span>
				</li>
				<li><?php echo $this->lang->line('inquiry_detail_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
				<div class="row">
					<div class="col-sm-6">
						<legend><?php echo $this->lang->line('inquiry_info_label')?></legend>
						<table class="table table-striped table-bordered">
							<tr>
								<th><?php echo $this->lang->line('item_name_label')?></th>
								<td><?php echo $this->item->get_info($inquiry->item_id)->name;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('name_label')?></th>
								<td><?php echo $inquiry->name;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('email_label')?></th>
								<td><?php echo $inquiry->email;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('message_label')?></th>
								<td><?php echo $inquiry->message;?></td>
							</tr>
						</table>
					</div>
				</div>
					
				<a class="btn btn-primary" href="<?php echo site_url('inquiries');?>" class="btn"><?php echo $this->lang->line('back_button')?></a>
			</div>