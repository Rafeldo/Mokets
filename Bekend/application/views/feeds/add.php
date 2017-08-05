			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('feeds');?>"><?php echo $this->lang->line('feed_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_feed_button')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
			$attributes = array('id' => 'feed-form','enctype' => 'multipart/form-data');
			echo form_open(site_url('feeds/add'), $attributes);
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
										'value' => '',
										'class' => 'form-control',
										'placeholder' => 'Title',
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
								<textarea class="form-control" name="description" placeholder="Description" rows="9"></textarea>
							</div>
					</div>
				</div>
				
				<hr/>
				
				<input type="submit" name="save" value="<?php echo $this->lang->line('save_button')?>" class="btn btn-primary"/>
				<input type="submit" name="gallery" value="<?php echo $this->lang->line('save_go_button')?>" class="btn btn-primary"/>
				<a href="<?php echo site_url('feeds');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
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
						title:{
							required: "Please fill title.",
							minlength: "The length of title must be greater than 4"
						}
					}
				});
			});
			
			$(function () { $("[data-toggle='tooltip']").tooltip(); });
			
			</script>

