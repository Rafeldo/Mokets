			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url('shops');?>"><?php echo $this->lang->line('shops_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('shop_info_label')?></li>
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
			$attributes = array('id' => 'shop-form','enctype' => 'multipart/form-data');
			echo form_open(site_url("shops/create"), $attributes);
			?>
				<div class="row">
					
					<ul id="myTab" class="nav nav-tabs">
					   <li class="active"><a href="#shopinfo" data-toggle="tab">Shop Information</a></li>
					   <li><a href="#payment" data-toggle="tab">Payment Setting</a></li>
					   <li><a href="#currency" data-toggle="tab">Currency Setting</a></li>
					   <li><a href="#sender" data-toggle="tab">Sending Email Setting(For SMTP)</a></li>
					</ul>
					
					<div id="myTabContent" class="tab-content">
						
						<div class="tab-pane fade in active" id="shopinfo">

							<div class="row">
								
								<div class="col-md-6">

									<br/>

									<div class="form-group">
										<label><?php echo $this->lang->line('name_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_name_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>
										<input class="form-control" type="text" placeholder="Name" name='name' id='name'
										 value="<?php echo $shop->name;?>">
									</div>
									
									<div class="form-group">
										<label><?php echo $this->lang->line('description_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_desc_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>
										<textarea class="form-control" name="description" placeholder="Description" rows="9"><?php echo $shop->description;?></textarea>
									</div>
									
									<div class="form-group">
										<label><?php echo $this->lang->line('phone_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_phone_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>
										<input class="form-control" type="text" placeholder="Phone" name='phone' id='phone' value="<?php echo $shop->phone;?>">
									</div>
									
									<div class="form-group">
										<label><?php echo $this->lang->line('contact_email_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_email_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>
										<input class="form-control" type="text" placeholder="Email" name='email' id='email' value="<?php echo $shop->email;?>">
									</div>
									
									<div class="form-group">
										<label><?php echo $this->lang->line('shop_cover_photo_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_photo_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label> 
										<br>
										<?php echo $this->lang->line('shop_image_recommended_size')?>
										<input class="btn" type="file" name="images1">
										<br/>
										<label><?php echo $this->lang->line('photo_desc_label')?> 
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_photo_desc_tooltips')?>">
											<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a></label>
										<textarea class="form-control" name="image_desc" rows="9"></textarea>
									</div>

								</div>

								<div class="col-md-6">

									<br/>
									<!--
									<div class="form-group">
										<label><?php echo $this->lang->line('find_location_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" 
												title="<?php echo $this->lang->line('find_location_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>

										<br>

										<?php 
											echo form_input( array(
												'type' => 'text',
												'name' => 'find_location',
												'id' => 'find_location',
												'class' => 'form-control',
												'placeholder' => 'Type & Find Location',
												'value' => ''
											));
										?>

									</div>

									<div id="us3" style="height: 300px;"></div> -->
										
									<div class="clearfix">&nbsp;</div>
										
									<div class="m-t-small">
										
									<div class="form-group">
										<label><?php echo $this->lang->line('shop_lat_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" 
												title="<?php echo $this->lang->line('shop_lat_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>

										<br>

										<?php 
											echo form_input( array(
												'type' => 'text',
												'name' => 'lat',
												'id' => 'lat',
												'class' => 'form-control',
												'placeholder' => '',
												'value' => ''
											));
										?>
									</div>

									<div class="form-group">
										<label><?php echo $this->lang->line('shop_lng_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" 
												title="<?php echo $this->lang->line('shop_lng_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>

										<br>

										<?php 
											echo form_input( array(
												'type' => 'text',
												'name' => 'lng',
												'id' => 'lng',
												'class' => 'form-control',
												'placeholder' => '',
												'value' => ''
											));
										?>
										</div>
									</div>
										
									<div class="form-group">
										<label><?php echo $this->lang->line('address_label')?>
											<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('shop_address_tooltips')?>">
												<span class='glyphicon glyphicon-info-sign menu-icon'>
											</a>
										</label>
										<textarea class="form-control" name="address" placeholder="Address" rows="9"><?php echo $shop->address;?></textarea>
									</div>

								</div>

							</div>
						</div>
					
					
					<div class="tab-pane fade" id="payment">
						<div class="form-group">
							<br>
							
							<label><?php echo $this->lang->line('stripe_label')?></label>
							
							<br>
		
							<label><?php echo $this->lang->line('stripe_publishable_key')?>
		
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('stripe_publishable_key_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
								
								
								
							</label>
							
							<input class="form-control" type="text" placeholder="Publishable Key" name='stripe_publishable_key' id='stripe_publishable_key'>
								 <br>
							
							
							<label><?php echo $this->lang->line('stripe_secret_key')?>
						
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('stripe_secret_key_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
								
							</label>
		
							<input class="form-control" type="text" placeholder="Secret Key" name='stripe_secret_key' id='stripe_secret_key'>
								 <br>
		
							<div class="form-group">
								<label><?php echo $this->lang->line('stripe_enable_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('stripe_enable_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
								: 
								</label>
								<?php
									echo form_checkbox("stripe_enabled",$shop->stripe_enabled);
								 ?>
							</div>
							
							<br>
							
							<hr>
							
							<label>
							<?php echo $this->lang->line('bank_transfer_label')?>
							</label>
							<br>
							
							<label><?php echo $this->lang->line('bank_account_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('bank_account_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>	
							</label>
							<input class="form-control" type="text" placeholder="Bank Account" name='bank_account' id='bank_account'>
							 <br>
							 
							<label><?php echo $this->lang->line('bank_name_label')?>
							 	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('bank_name_tooltips')?>">
							 		<span class='glyphicon glyphicon-info-sign menu-icon'>
							 	</a>
							</label>
							<input class="form-control" type="text" placeholder="Bank Name" name='bank_name' id='bank_name'>
							<br>
							  
							<label><?php echo $this->lang->line('bank_code_label')?>
							    <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('bank_code_tooltips')?>">
							  	 	<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>  	
							</label>
							<input class="form-control" type="text" placeholder="Bank Code" name='bank_code' id='bank_code'>
							<br>
							   
						    <label><?php echo $this->lang->line('branch_code_label')?>
							  	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('branch_code_tooltips')?>">
							  		<span class='glyphicon glyphicon-info-sign menu-icon'>
							  	</a>
						    </label>
						    <input class="form-control" type="text" placeholder="Branch Name" name='branch_code' id='branch_code'>
						    <br>
							   
						    <label><?php echo $this->lang->line('swift_code_label')?>
							   	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('swift_code_tooltips')?>">
							   		<span class='glyphicon glyphicon-info-sign menu-icon'>
							   	</a>
						    </label>
						    <input class="form-control" type="text" placeholder="Swift Code" name='swift_code' id='swift_code'>
							
							<br>
							<div class="form-group">
								<label><?php echo $this->lang->line('bank_enable_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('bank_enable_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
								: 
								</label>
								<?php
									echo form_checkbox("banktransfer_enabled",$shop->banktransfer_enabled);
								 ?>
							</div>
		
							<br>
							  
							<hr>
							
							<label>
							<?php echo $this->lang->line('cod_label')?>
							</label>
							<br>
							<label><?php echo $this->lang->line('cod_email_label')?>
							   	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('code_email_tooltips')?>">
							   		<span class='glyphicon glyphicon-info-sign menu-icon'>
							   	</a>
							</label>
							<input class="form-control" type="text" placeholder="COD Confirmation Email" name='cod_email' id='cod_email'>
							
							<br>
							<div class="form-group">
								<label><?php echo $this->lang->line('cod_enable_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('cod_enable_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
								: 
								</label>
								<?php
									echo form_checkbox("cod_enabled",$shop->cod_enabled);
								 ?>
							</div>
							
						</div>
					</div>
					
					<div class="tab-pane fade" id="currency">
						 <br>				     
					     <div class="col-sm-7">
					     	<div class="form-group">
					     		<label><?php echo $this->lang->line('currency_additional_info')?> </label> <br><br>
					     	
					     		<label><?php echo $this->lang->line('currency_symbol_label')?> 
					     			<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('currency_symbol_tooltips')?>">
					     				<span class='glyphicon glyphicon-info-sign menu-icon'>
					     			</a>
					     		</label>
					     		<input class="form-control" type="text" placeholder="Currency Symbol" name='currency_symbol' id='currency_symbol'>
					     	</div>
					     	
					     							
					     	<div class="form-group">
					     		<label><?php echo $this->lang->line('currency_form_label')?> 
					     			<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('currency_form_tooltips')?>">
					     				<span class='glyphicon glyphicon-info-sign menu-icon'>
					     			</a>
					     		</label>
					     		<input class="form-control" type="text" placeholder="Currency Short Form" name='currency_short_form' id='currency_short_form'>
					     	</div>
					     		
					     	
					     </div>
					 </div>
					 
					 <div class="tab-pane fade" id="sender">
						 <br>
						 <div class="col-sm-6">
						 	<div class="form-group">
					 			<label><?php echo $this->lang->line('sender_email_label')?> 
					 				<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo $this->lang->line('sender_email_tooltips')?>">
					 					<span class='glyphicon glyphicon-info-sign menu-icon'>
					 				</a>
					 			</label>
					 			<input class="form-control" type="text" placeholder="Sender Email" name='sender_email' id='sender_email'>
					 		</div>
						 </div>
					 </div>
				   </div>		
				</div>
				
				<hr/>
				
				<button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save_button')?></button>
				
				<a href="<?php echo site_url('shops');?>" class="btn btn-primary"><?php echo $this->lang->line('cancel_button')?></a>
			</form>
			<script>
				$(document).ready(function(){
					$(function () { $("[data-toggle='tooltip']").tooltip(); });
				});
				
				/*
				$('#us3').locationpicker({
				    location: {latitude: 0.0, longitude: 0.0},
				    radius: 300,
				    inputBinding: {
				        latitudeInput: $('#lat'),
				        longitudeInput: $('#lng'),
				        radiusInput: $('#us3-radius'),
				        locationNameInput: $('#find_location')
				    },
				    enableAutocomplete: true,
				    onchanged: function (currentLocation, radius, isMarkerDropped) {
				        // Uncomment line below to show alert on each Location Changed event
				        //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
				    }
				});
				*/
				
				$('#shop-form').validate({
					rules:{
						name:{
							required: true
						},
						description:{
							required : true
						},
						email: {
							required: true,
							email: true
						}
					},
					messages:{
						name:{
							required: "Please Fill Shop Name."
						},
						description:{
							required: "Please Fill Shop Description."
						},
						email: {
							email: "Email format is wrong.",
							required : "Please Fill Shop Email."
						}
					}
				});	
			</script>