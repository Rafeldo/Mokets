			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('discount_types');?>"><?php echo $this->lang->line('discount_types_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_discount_button')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
				$attributes = array('id' => 'discount_type-form', 'enctype' => 'multipart/form-data');
				echo form_open(site_url('discount_types/add'), $attributes);
			?>
				<legend><?php echo $this->lang->line('discount_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label><?php echo $this->lang->line('discount_types_name_label')?></label>

								<?php
									echo form_input(array(
										'name' => 'name',
										'value' => '',
										'class' => 'form-control',
										'placeholder' => 'Discount Type Name',
										'id' => 'name'
									));
								?>
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('discount_percent_label')?></label>

								<?php 
									echo form_input(array(
										'name' => 'percent',
										'value' => '',
										'class' => 'form-control',
										'placeholder' => 'Percent',
										'id' => 'percent'
									));
								?>
							</div>
							
							<div class="form-group">
								<label> <?php echo $this->lang->line('choose_item_label')?> </label>
								<div  style="max-height: 30%; overflow-y:  scroll;">
									<?php
										$shop_id = $this->shop->get_current_shop()->id;
										$items = $this->item->get_all($shop_id);
										foreach ($items->result() as $item) {
									?>
											<div class="checkbox">
											  <label>
											    <input type="checkbox" name="items[]" value="<?php echo $item->id;?>">
											    <?php echo $item->name;?>
											  </label>
											</div>
									<?php
										}
									?>
								</div>
							</div>
					</div>
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('discount_types');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			</div>
			<script>
				$(document).ready(function(){
					$('#discount_type-form').validate({
						rules:{
							name:{
								required: true,
								minlength: 3,
								remote: '<?php echo site_url("discount_types/exists/" . $this->shop->get_current_shop()->id);?>'
							},
							percent:{
								required: true,
								number: true
							}
						},
						messages:{
							name:{
								required: "Please fill Discount Type Name.",
								minlength: "The length of Discount Type Name must be greater than 4",
								remote: "Discount Type Name is already existed in the system."
							},
							percent:{
								required: "Please fill percent",
								number: "Please fill number only"
							}
						}
					});
				});
			</script>