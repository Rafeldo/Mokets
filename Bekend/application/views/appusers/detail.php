			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('appuser_info_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
				<div class="row">
					<div class="col-sm-6">
						<legend><?php echo $this->lang->line('appuser_detail_label')?></legend>
						
						<table class="table table-striped table-bordered">
							<tr>
								<th><?php echo $this->lang->line('username_label')?></th>
								<td><?php echo $appuser->username;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('email_label')?></th>
								<td><?php echo $appuser->email;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('aboutme_label')?></th>
								<td><?php echo $appuser->about_me;?></td>
							</tr>
						</table>
					</div>
					
					<div class="col-sm-6">
						
					</div>
				</div>
					
				<a class="btn btn-primary" href="<?php echo site_url('appusers');?>" class="btn"><?php echo $this->lang->line('back_button')?></a>
			</div>
