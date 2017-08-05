			<?php
				$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('attributes');?>"><?php echo $this->lang->line('att_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('add_new_att_button')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
				$attributes = array('id' => 'attribute_header-form','enctype' => 'multipart/form-data');
				echo form_open(site_url('attributes/add'), $attributes);
			?>
				<legend><?php echo $this->lang->line('att_info_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label> <?php echo $this->lang->line('item_name_label')?> 
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('item_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>
								<select class="form-control" name="item_id" id="item_id">
								<?php
									$items = $this->item->get_all($this->shop->get_current_shop()->id);
									foreach ($items->result() as $item) {
										echo "<option value='". $item->id ."'>". $item->name ."</option>";
									}
								?>
								</select>
							</div>
					
							<div class="form-group">
								<label><?php echo $this->lang->line('att_name_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('att_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input(array(
									'name' => 'name',
									'class' => 'form-control',
									'placeholder' => 'Attribute Name',
									'id' => 'name'
								)); ?>
							</div>
							
					</div>
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('attributes');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			</div>
			<script>
				$(document).ready(function(){
					$('#attribute_header-form').validate({
						rules:{
							name:{
								required: true,
								minlength: 2,
								remote: {
									url: '<?php echo site_url("attributes/exists/".$this->shop->get_current_shop()->id);?>',
								  	type: "GET",
								  	data: {
								    	item_id: function() {
								    		return $('#item_id').val();
								    	}
								  	}
								}
							}
						},
						messages:{
							name:{
								required: "Please fill for Attribute Name.",
								minlength: "The length of Attribute Name must be greater than 2",
								remote: "Attribute Name is already existed in the system"
							}
						}
					});
				});
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
			</script>
