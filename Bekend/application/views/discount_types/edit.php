			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('discount_types');?>"><?php echo $this->lang->line('discount_types_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('update_discount_type_label')?></li>
			</ul>
			<div class="wrapper wrapper-content animated fadeInRight">
			<?php
			$attributes = array('id' => 'discount_type-form');
			echo form_open(site_url('discount_types/edit/'.$discount_type->id), $attributes);
			?>
				<div class="row">
					<div class="col-sm-6">
						<legend><?php echo $this->lang->line('discount_type_info_label')?></legend>
						
						<div class="form-group">
							<label><?php echo $this->lang->line('discount_types_name_label')?></label>

							<?php
								echo form_input(array(
									'name' => 'name',
									'value' => html_entity_decode( $discount_type->name ),
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
									'value' => html_entity_decode( $discount_type->percent ),
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
										    <input type="checkbox" name="items[]" value="<?php echo $item->id;?>"
										    <?php echo ($discount_type->id == $item->discount_type_id)? "checked": ""; ?>>
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
			