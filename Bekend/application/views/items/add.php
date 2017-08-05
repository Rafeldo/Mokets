			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('items');?>"><?php echo $this->lang->line('item_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_item_button')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
			$attributes = array('id' => 'item-form','enctype' => 'multipart/form-data');
			echo form_open(site_url('items/add'), $attributes);
			?>
				<legend><?php echo $this->lang->line('item_info_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label><?php echo $this->lang->line('item_name_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('item_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'name',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => $this->lang->line('item_name_label'),
									'id' => ''
								)); ?>

							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('cat_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cat_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<select class="form-control" name="cat_id" id="cat_id">
								<option><?php echo $this->lang->line('select_cat_message')?></option>
								<?php
									$categories = $this->category->get_all($this->shop->get_current_shop()->id);
									foreach($categories->result() as $cat)
										echo "<option value='".$cat->id."'>".$cat->name."</option>";
								?>
								</select>
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('sub_cat_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('sub_cat_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<select class="form-control" name="sub_cat_id" id="sub_cat_id">
									<option><?php echo $this->lang->line('select_sub_cat_message')?></option>
								</select>
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('discount_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('discount_type_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<select class="form-control" name="discount_type_id" id="discount_type_id">
									<option><?php echo $this->lang->line('select_discount_message')?></option>
									<?php
										$discounts = $this->discount_type->get_all($this->shop->get_current_shop()->id);
										foreach($discounts->result() as $dis)
											echo "<option value='".$dis->id."'>".$dis->name."</option>";
									?>
								</select>
							</div>
							
					</div>
					
					<div class="col-sm-6">
					
							<div class="form-group">
								<label><?php echo $this->lang->line('unit_price_label')?> 
								(<?php echo $this->lang->line('default_currency_label')?> : <?php echo $this->shop->get_current_shop()->currency_short_form;?>)
								
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('price_tooltips')?>">
								    	<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<table width="100%">
									<tr>
										<td width= "90%">

											<?php echo form_input(array(
												'name' => 'unit_price',
												'value' => '',
												'class' => 'form-control',
												'placeholder' => $this->lang->line('unit_price_label'),
												'id' => 'unit_price'
											)); ?>

										</td>
										<td width= "10%"> <b>&nbsp;&nbsp; <?php echo $this->shop->get_current_shop()->currency_symbol; ?></b> </td>
									</tr>
								</table>
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('search_tag_label')?> 
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('search_tag_tooltips')?>">
								    	<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'search_tag',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => $this->lang->line('search_tag_label'),
									'id' => ''
								)); ?>
								
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('description_label')?> 
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('item_description_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<textarea class="form-control" name="description" placeholder="<?php echo $this->lang->line('description_label')?>" rows="8"></textarea>
							</div>
					</div>
				</div>
				
				<input type="submit" name="save" value="<?php echo $this->lang->line('save_button')?>" class="btn btn-primary"/>
				<input type="submit" name="gallery" value="<?php echo $this->lang->line('save_go_button')?>" class="btn btn-primary"/>
				<a href="<?php echo site_url('items');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			</div>
			<script>
			$(document).ready(function(){
				$('#item-form').validate({
					rules:{
						name:{
							required: true,
							minlength: 4,
							remote: {
								url: '<?php echo site_url("items/exists");?>',
					        	type: "GET",
					        	data: {
					        		name: function () {
					        			return $('#name').val();
					        		},
					          	sub_cat_id: function() {
					          		return $('#sub_cat_id').val();
					          	}
					        	}
							}
						},
						unit_price: {
							number: true
						}
					},
					messages:{
						name:{
							required: "Please fill item Name.",
							minlength: "The length of item Name must be greater than 4",
							remote: "Item Name is already existed in the system"
						},
						unit_price: {
							number: "Only number is allowed."
						}
					}
				});
				
				$('#cat_id').change(function(){
					var catId = $(this).val();
					$.ajax({
						url: '<?php echo site_url('items/get_sub_cats');?>/'+catId,
						method: 'GET',
						dataType: 'JSON',
						success:function(data){
							$('#sub_cat_id').html("");
							$.each(data, function(i, obj){
							    $('#sub_cat_id').append('<option value="'+ obj.id +'">' + obj.name + '</option>');
							});
							$('#name').val($('#name').val() + " ").blur();
						}
					});
				});
				
				$('#sub_cat_id').on('change', function(){
					$('#name').val($('#name').val() + " ").blur();
				});
				
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
			});
			</script>

