			<?php
				$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('sub_categories');?>"><?php echo $this->lang->line('sub_categories_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_sub_cat_button')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
				$attributes = array('id' => 'sub_category-form','enctype' => 'multipart/form-data');
				echo form_open(site_url('sub_categories/add'), $attributes);
			?>
				<legend><?php echo $this->lang->line('sub_cat_info_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label> <?php echo $this->lang->line('category_name_label')?> 
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cat_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<select class="form-control" name="cat_id" id="cat_id">
								<?php
									$categories = $this->category->get_all($this->shop->get_current_shop()->id);
									foreach ($categories->result() as $category) {
										echo "<option value='". $category->id ."'>". $category->name ."</option>";
									}
								?>
								</select>
							</div>
					
							<div class="form-group">
								<label><?php echo $this->lang->line('sub_category_name_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('sub_cat_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'name',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => 'Sub Category Name',
									'id' => 'name'
								)); ?>

							</div>
							<div class="form-group">
								<label><?php echo $this->lang->line('ordering_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('sub_cat_ordering_tooltips')?>">
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
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('sub_cat_photo_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<input class="btn" type="file" name="images1">
							</div>
					</div>
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('sub_categories');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			</div>
			<script>
				$(document).ready(function(){
					$('#sub_category-form').validate({
						rules:{
							name:{
								required: true,
								minlength: 2,
								remote: '<?php echo site_url("sub_categories/exists/" . $this->shop->get_current_shop()->id);?>'
							}
						},
						messages:{
							name:{
								required: "Please fill for Sub Category Name.",
								minlength: "The length of Sub Category Name must be greater than 2",
								remote: "Sub Category Name is already existed in the system"
							}
						}
					});
				});
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
			</script>
