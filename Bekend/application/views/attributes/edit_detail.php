			<?php
				$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url(). "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('attributes');?>"><?php echo $this->lang->line('att_detail_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('update_att_detail_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
				$form_attributes = array('id' => 'attribute_detail-form','enctype' => 'multipart/form-data');
				echo form_open(site_url('attributes/edit_detail/' . $attributes_detail->id), $form_attributes);
			?>
				<legend><?php echo $this->lang->line('att_detail_info_label')?></legend>
					
				<div class="row">
					<div class="col-sm-6">
							<div class="form-group">
								<label> 
									<?php echo $this->lang->line('item_name_label')?>  : 
									<?php echo $this->item->get_info($attributes_detail->item_id)->name; ?>
								</label>
							</div>
					
							<div class="form-group">
								<label>
									<?php echo $this->lang->line('att_name_label')?> : 
									<?php echo $this->attribute_header->get_info($attributes_detail->header_id)->name; ?>
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
									'value' => html_entity_decode( $attributes_detail->name ),
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
								<input class="form-control" type="text" placeholder="Attribute Additional Price" name='additional_price' id='additional_price' value="<?php echo $attributes_detail->additional_price;?>">
							</div>
							
							
					</div>
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				<a href="<?php echo site_url('attributes');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
				<input type="hidden" name="header_id" id="header_id" value="<?php echo $attributes_detail->header_id; ?>" />
				<input type="hidden" name="item_id" id="item_id" value="<?php echo $attributes_detail->item_id; ?>" />
			</form>
			</div>
			<script>
				$(document).ready(function(){
					$('#attribute-form').validate({
						rules:{
							name:{
								required: true,
								minlength: 2,
								remote: {
									url: '<?php echo site_url("items/exists/".$this->shop->get_current_shop()->id);?>',
								  	type: "GET",
								  	data: {
								  		name: function () {
								  			return $('#name').val();
								  		},
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
