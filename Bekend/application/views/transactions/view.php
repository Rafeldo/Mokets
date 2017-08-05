			<?php
				$this->lang->load('ps', 'english');
			?>
			<div class='row'>
				<div class='col-sm-12'>
					<ul class="breadcrumb">
						<li><a href="<?php echo site_url("dashboard");?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
						<li><?php echo $this->lang->line('transactions_list_label')?></li>
					</ul>
				</div>
			</div>
			
			<div class='row'>
				<div class='col-sm-12'>
					<?php
					$attributes = array('class' => 'form-inline');
					echo form_open(site_url('transactions/search'), $attributes);
					?>
						<div class="form-group">

						   	<?php echo form_input(array(
						   		'name' => 'searchterm',
						   		'value' => html_entity_decode( $searchterm ),
						   		'class' => 'form-control',
						   		'placeholder' => 'Search',
						   		'id' => '',
						   		'style' => 'float: left; margin-right: 20px;'
						   	)); ?>
					  	
		  	               <div class="input-group date form_datetime col-md-3" 
		  	               		data-date-format="yyyy-mm-dd" data-link-field="dtp_input1" style="float: left; margin-right: 20px;">

								<?php echo form_input(array(
									'name' => 'start_date',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => '',
									'id' => '',
									'size' => '16',
									'readonly' => 'readonly'
								)); ?>

			  					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
		  	               </div>
	  					   <input type="hidden" id="dtp_input1" value=""/>
	  						
	  					   <div class="input-group date form_datetime col-md-3" 
	  						  	data-date-format="yyyy-mm-dd" data-link-field="dtp_input1" style="float: left; margin-right: 20px">
	  						    
								<?php echo form_input(array(
									'name' => 'end_date',
									'value' => '',
									'class' => 'form-control',
									'placeholder' => '',
									'id' => '',
									'size' => '16',
									'readonly' => 'readonly'
								)); ?>

	  							<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
	  					   </div>
	  						<input type="hidden" id="dtp_input1" value=""/><br/>
	  						
	  						<button type="submit" class="btn btn-default" style="margin-top: -25px"><?php echo $this->lang->line('search_button')?></button>
	  	              	</div>
	  	              	
					  	
					</form>
				</div>	
			</div>
			
			<br/>
			
			
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			
			<?php
				if (count($transactions) > 0) {
					foreach ($transactions as $trans) {
						$details = $this->transaction_detail->get_all_by_header($trans->id)->result();
			?>
				<div class="wrapper wrapper-content animated fadeInRight">
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
								<?php 
									echo $this->appuser->get_info($trans->user_id)->username;
									echo "<small style='padding: 0 50px'>". count($details) ." Items</small>";
									echo "<small style='padding: 0 50px'>".$this->common->date_formatting($trans->added)."</small>";
								?>
								<a class="pull-right label label-info" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $trans->id;?>" aria-expanded="true" aria-controls="collapseOne">
									Detail
								</a>
							</h4>
						</div>
						<div id="<?php echo $trans->id;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-4">
										<?php echo "<p><u><b>Billing Address</b></u><br/>". $trans->delivery_address ."</p>";?>
									</div>
									<div class="col-md-4">
										<?php echo "<p><u><b>Delivery Address</b></u><br/>". $trans->billing_address ."</p>";?>
									</div>
									<div class="col-md-4">
										<?php
										$attributes = array('class' => 'form-inline');
										echo form_open(site_url('transactions/update'), $attributes);
										?>
											<input type="hidden" name="header_id" id="header_id" 
												value="<?php echo $trans->id;?>" />
											Transaction Status : 
											<select  name="transaction_status" id="transaction_status">
											<?php
												
												$status = $this->transaction_status->get_all();
													foreach ($status->result() as $status) {
														echo "<option value='".$status->id."'";
														if($trans->transaction_status == $status->id) {
															echo " selected ";
														}
														echo ">".$status->title."</option>";
													}
													
											?>
											</select>
											<button type="submit" class="btn btn-primary" style="padding : 2px 5px;"><?php echo $this->lang->line('update_button')?></button>
										</form>
									</div>
								</div>
								<?php
									echo "<table class='table table-condensed'>";
									echo "<tr><th>Item Name</th><th>Unit Price</th><th>Qty</th><th>Discount(%)</th><th>Amount</th>";
									
									$total_amount = 0;
									foreach ($details as $d) {
										echo "<tr>";
										echo "<td>". $this->item->get_info($d->item_id)->name ."</td>";
										echo "<td>". $this->shop->get_current_shop()->currency_symbol . $d->unit_price ."</td>";
										echo "<td>". $d->qty ."</td>";
										echo "<td>". $d->discount_percent * 100 ."</td>";
										//$amount = ($d->qty * $d->unit_price) - ($d->unit_price * $d->discount_percent / 100);
										$amount = ($d->qty * $d->unit_price);
										
										$total_amount += $amount;
										echo "<td>". $this->shop->get_current_shop()->currency_symbol . $amount ."</td>";
										echo "</tr>";
									}
									
									echo "<tr><td colspan='4'>Total Amount</td><td>". $this->shop->get_current_shop()->currency_symbol . $total_amount ."</td></tr>";
									echo "</table>";
								?>
							</div>
						</div>
					</div>
				</div>
			<?php 
					}
				} else {
			?>
					<?php echo $this->lang->line('no_transactions_data_message');?>
			<?php
				}
				
				$this->pagination->initialize($pag);
				echo $this->pagination->create_links();
			?>
			
			<script type="text/javascript">
			  $('.form_datetime').datetimepicker({
			         minView: 2,
			         format: 'yyyy-mm-dd'
			     });
			</script>