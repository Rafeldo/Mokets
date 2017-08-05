			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li>
					<a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> 
					<span class="divider"></span>
				</li>
				<li><?php echo $this->lang->line('detail_review_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
				<div class="row">
					<div class="col-sm-6">
						<legend><?php echo $this->lang->line('review_info_label')?></legend>
						
						<table class="table table-striped table-bordered">
							<tr>
								<th><?php echo $this->lang->line('item_name_label')?></th>
								<td><?php echo $this->item->get_info($review->item_id)->name;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('appuser_name_label')?></th>
								<td><?php echo $this->appuser->get_info($review->appuser_id)->username;?></td>
							</tr>
							<tr>
								<th><?php echo $this->lang->line('about_review_label')?></th>
								<td><?php echo $review->review;?></td>
							</tr>
						</table>
					</div>
				</div>
					
				<a class="btn btn-primary" href="<?php echo site_url('reviews');?>" class="btn"><?php echo $this->lang->line('back_button')?></a>
			</div>
