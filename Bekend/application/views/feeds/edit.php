			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('feeds');?>"><?php echo $this->lang->line('feed_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('update_feed_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
			$this->lang->load('ps', 'english');
			$attributes = array('id' => 'feed-form','enctype' => 'multipart/form-data');
			echo form_open(site_url("feeds/edit/".$feed->id), $attributes);
			?>
				<legend><?php echo $this->lang->line('feed_info_lable')?></legend>
				
				<div class="row">
					<div class="col-sm-8">
						<div class="form-group">
							<label><?php echo $this->lang->line('feed_title_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('feed_title_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>
							
							<?php 
								echo form_input(array(
									'name' => 'title',
									'value' => html_entity_decode( $feed->title ),
									'class' => 'form-control',
									'placeholder' => 'title',
									'id' => 'title'
								));
							?>
						</div>
						
						<div class="form-group">
							<label><?php echo $this->lang->line('description_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('feed_description_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>
							<textarea class="form-control" name="description" placeholder="Description" rows="9"><?php echo $feed->description;?></textarea>
						</div>
						
						<input type="submit" value="<?php echo $this->lang->line('update_button')?>" class="btn btn-primary"/>
						<a class="btn btn-primary" href="<?php echo site_url('feeds/gallery/'.$feed->id);?>"><?php echo $this->lang->line('goto_gallery_button')?></a>
						<a href="<?php echo site_url('feeds');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
					</div>
				</div>
			</form>
			</div>			
			<script>
				$(document).ready(function(){
					$('#feed-form').validate({
						rules:{
							title:{
								required: true,
								minlength: 4
							}
						},
						messages:{
							name:{
								required: "Please fill feed name.",
								minlength: "The length of feed name must be greater than 4"
							}
						}
					});				
				});
				
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
				
				
			</script>

