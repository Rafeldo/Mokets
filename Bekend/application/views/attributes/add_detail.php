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
				$form_attributes = array('id' => 'attribute_detail-form','enctype' => 'multipart/form-data');
				echo form_open(site_url('attributes/add_detail/' . $attributes_header->id), $form_attributes);
			?>
				<legend><?php echo $this->lang->line('att_detail_info_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label> 
									<?php echo $this->lang->line('item_name_label')?>  : 
									<?php echo $this->item->get_info($attributes_header->item_id)->name; ?>
								</label>
							</div>
					
							<div class="form-group">
								<label>
									<?php echo $this->lang->line('att_name_label')?> : 
									<?php echo $attributes_header->name; ?>
								</label>	
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('att_detail_name_label')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('att_detail_name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
									
								</label>
								<br>
								(<i><?php echo $this->lang->line('att_detail_name_additional'); ?></i>)

								<?php echo form_input(array(
									'name' => 'name',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => 'Attribute Detail Name',
									'id' => 'name'
								)); ?>
								
							</div>
							
							<div class="form-group">
								<label><?php echo $this->lang->line('att_additional_price')?>
								&nbsp;(<?php echo $this->shop->get_current_shop()->currency_symbol; ?>)
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('att_additional_price_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
									
								</label>
								<br>
								( <i><?php echo $this->lang->line('att_additional_price_message'); ?></i> )
								<input class="form-control" type="text" placeholder="Attribute Additional Price" name='additional_price' id='additional_price'>
							</div>
							
					</div>
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('attributes');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
				<input type="hidden" name="header_id" id="header_id" value="<?php echo $attributes_header->id; ?>" />
				<input type="hidden" name="item_id" id="item_id" value="<?php echo $attributes_header->item_id; ?>" />
			</form>
			</div>
			<script>
				$(document).ready(function(){
					$('#attribute_detail-form').validate({
						rules:{
							name:{
								required: true,
								remote: {
									url: '<?php echo site_url("attributes/exists_detail/".$this->shop->get_current_shop()->id);?>',
								  	type: "GET",
								  	data: {
								    	header_id: function() {
								    		return $('#header_id').val();
								    	}
								  	}
								}
							}
						},
						messages:{
							name:{
								required: "Please fill for Attribute Detail Name.",
								remote: "Attribute Detail Name is already existed in the system"
							}
						}
					});
				});
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
			</script>
