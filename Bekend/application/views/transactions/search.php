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
				<div class='col-sm-9'>
					<?php
					$attributes = array('class' => 'form-inline');
					echo form_open(site_url('transactions/search'), $attributes);
					?>

					   	<?php echo form_input(array(
					   		'name' => 'searchterm',
					   		'value' => html_entity_decode( $searchterm ),
					   		'class' => 'form-control',
					   		'placeholder' => 'Search',
					   		'id' => '',
					   		'style' => 'float: left; margin-right: 20px;'
					   	)); ?>
					  	
			              <div class="input-group date form_datetime col-md-3" 
			              		data-date-format="yyyy-mm-dd HH:ii:ss" data-link-field="dtp_input1" style="float: left; margin-right: 20px;">

								<?php echo form_input(array(
									'name' => 'start_date',
									'value' => $start_date,
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
							  	data-date-format="yyyy-mm-dd HH:ii:ss" data-link-field="dtp_input1" style="float: left; margin-right: 20px">

								<?php echo form_input(array(
									'name' => 'end_date',
									'value' => $end_date,
									'class' => 'form-control',
									'placeholder' => '',
									'id' => '',
									'size' => '16',
									'readonly' => 'readonly'
								)); ?>

								<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
						   </div>
						   <input type="hidden" id="dtp_input1" value=""/>
			           		
			           	   <div class="input-group col-md-3">
					  	    <button type="submit" class="btn btn-default" ><?php echo $this->lang->line('search_button')?></button>
					 		<a href='<?php echo site_url('transactions');?>' class="btn btn-default" ><?php echo $this->lang->line('reset_button')?></a>
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
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingOne">
						<h4 class="panel-title">
							<?php 
								echo $this->appuser->get_info($trans->user_id)->username;
								echo "<small style='padding: 0 50px'>". count($details) ." Items</small>";
								echo "<small style='padding: 0 50px'>$trans->added</small>";
							?>
							<a class="pull-right label label-info" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $trans->id;?>" aria-expanded="true" aria-controls="collapseOne">
								Detail
							</a>
						</h4>
					</div>
					<div id="<?php echo $trans->id;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<?php echo "<p><u>Billing Address</u><br/>". $trans->delivery_address ."</p>";?>
								</div>
								<div class="col-md-6">
									<?php echo "<p><u>Delivery Address</u><br/>". $trans->billing_address ."</p>";?>
								</div>
							</div>
							<?php
								echo "<table class='table table-condensed'>";
								echo "<tr><th>Item Name</th><th>Unit Price</th><th>Qty</th><th>Disc(%)</th><th>Amount</th>";
								
								$total_amount = 0;
								foreach ($details as $d) {
									echo "<tr>";
									echo "<td>". $this->item->get_info($d->item_id)->name ."</td>";
									echo "<td>". $this->shop->get_current_shop()->currency_symbol . $d->unit_price ."</td>";
									echo "<td>". $d->qty ."</td>";
									echo "<td>". $d->discount_percent ."</td>";
									$amount = ($d->qty * $d->unit_price) - ($d->unit_price * $d->discount_percent / 100);
									$total_amount += $amount;
									echo "<td>". $this->shop->get_current_shop()->currency_symbol . $amount ."</td>";
									echo "</tr>";
								}
								
								echo "<tr><td colspan='4'>Total Amount</td><td>". $total_amount ."</td></tr>";
								echo "</table>";
							?>
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
			         //language:  'fr',
			         weekStart: 1,
			         todayBtn:  1,
			  			autoclose: 1,
			  			todayHighlight: 1,
			  			startView: 2,
			  			forceParse: 0,
			         showMeridian: 0
			     });
			</script>