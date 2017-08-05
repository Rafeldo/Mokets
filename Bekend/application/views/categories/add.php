			<?php
				$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('categories');?>"><?php echo $this->lang->line('cat_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_cat_button')?></li>
			</ul>
			
			<?php
				$attributes = array('id' => 'category-form','enctype' => 'multipart/form-data');
				echo form_open(site_url('categories/add'), $attributes);
			?>
				<div class="wrapper wrapper-content animated fadeInRight">
				<legend><?php echo $this->lang->line('cat_info_lable')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label><?php echo $this->lang->line('category_name_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cat_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'name',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => 'Category Name',
									'id' => 'name'
								)); ?>

							</div>
							<div class="form-group">
								<label><?php echo $this->lang->line('ordering_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cat_ordering_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'ordering',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => 'Ordering',
									'id' => 'ordering'
								)); ?>

							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('cat_photo_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cat_photo_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<input class="btn" type="file" name="images1">
							</div>	
					</div>
					
				</div>
				
				<hr/>
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('categories');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
				
				<?php echo form_close(); ?>
			</div>

			<script>
				$(document).ready(function(){
					$('#category-form').validate({
						rules:{
							name:{
								required: true,
								minlength: 3,
								remote: '<?php echo site_url("categories/exists/" . $this->shop->get_current_shop()->id);?>'
							}
						},
						messages:{
							name:{
								required: "Please fill Category Name.",
								minlength: "The length of Category Name must be greater than 4",
								remote: "Category Name is already existed in the system."
							}
						}
					});
				});
				
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
			</script>
