			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url('dashboard') . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('currency_update_label')?></li>
			</ul>
		
			<!-- Message -->
			<?php if($this->session->flashdata('success')): ?>
				<div class="alert alert-success fade in">
					<?php echo $this->session->flashdata('success');?>
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				</div>
			<?php elseif($this->session->flashdata('error')):?>
				<div class="alert alert-danger fade in">
					<?php echo $this->session->flashdata('error');?>
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				</div>
			<?php endif;?>
			
			<?php
			$attributes = array('id' => 'currency-form');
			echo form_open(site_url("currencies/update/".$currency->id), $attributes);
			?>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label><?php echo $this->lang->line('currency_symbol_label')?> 
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('currency_symbol_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<?php echo form_input(array(
								'name' => 'currency_symbol',
								'value' => html_entity_decode( $currency->currency_symbol ),
								'class' => 'form-control',
								'placeholder' => 'Currency Symbol',
								'id' => 'currency_symbol'
							)); ?>
						</div>
						
												
						<div class="form-group">
							<label><?php echo $this->lang->line('currency_form_label')?> 
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('currency_form_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<?php 
								echo form_input(array(
									'name' => 'currency_short_form',
									'value' => html_entity_decode( $currency->currency_short_form ),
									'class' => 'form-control',
									'placeholder' => 'Currency Short Form',
									'id' => 'currency_short_form'
								));
							?>
						</div>
							
						
					</div>
					
				</div>
				<hr/>
				
				<input type="submit" value="<?php echo $this->lang->line('update_button')?>" class="btn btn-primary"/>
				<a href="<?php echo site_url('shops');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			
			<script>
				
				$(function () { $("[data-toggle='tooltip']").tooltip(); });
				
			</script>			