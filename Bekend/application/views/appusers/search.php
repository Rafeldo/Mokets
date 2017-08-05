			<?php
			$this->lang->load('ps', 'english');
			?>
			<ul class="breadcrumb">
				<li><a href="<?php echo site_url() . "/dashboard";?>"><?php echo $this->lang->line('dashboard_label')?></a> <span class="divider"></span></li>
				<li><a href="<?php echo site_url('appusers');?>"><?php echo $this->lang->line('appuser_list_label')?></a> <span class="divider"></span></li>
				<li><?php echo $this->lang->line('search_result_label')?></li>
			</ul>
			
			<?php
			$attributes = array('class' => 'form-inline');
			echo form_open(site_url('appusers/search'),$attributes);
			?>
				<div class="form-group">

			   		<?php echo form_input(array(
			   			'name' => 'searchterm',
			   			'value' => html_entity_decode( $searchterm ),
			   			'placeholder' => 'Search',
						'class' => 'form-control'
			   		)); ?>
			   		
			  	</div>
			  	<button type="submit" class="btn btn-default"><?php echo $this->lang->line('search_button')?></button>
			  	<a href='<?php echo site_url('appusers');?>' class="btn btn-default"><?php echo $this->lang->line('reset_button')?></a>
			</form>
			
			<br/>
			<div class="wrapper wrapper-content animated fadeInRight">
			<table class="table table-striped table-bordered">
				<tr>
					<th><?php echo $this->lang->line('no_label')?></th>
					<th><?php echo $this->lang->line('username_lable')?></th>
					<th><?php echo $this->lang->line('email_label')?></th>
					<th><?php echo $this->lang->line('detail_label')?></th>
					<?php if(in_array('ban',$allowed_accesses)):?>
						<th><?php echo $this->lang->line('ban_label')?></th>
					<?php endif;?>
				</tr>
				<?php
					if(!$count=$this->uri->segment(3))
						$count = 0;
					if(isset($appusers) && count($appusers->result())>0):
						foreach($appusers->result() as $appuser):					
				?>
						<tr>
							<td><?php echo ++$count;?></td>
							<td><?php echo $appuser->username;?></td>
							<td><?php echo $appuser->email;?></td>
							<td><a href='<?php echo site_url('appusers/detail/'.$appuser->id);?>'><?php echo $this->lang->line('detail_label')?></a></td>
							<?php if(in_array('ban',$allowed_accesses)):?>
							<td>
								<?php if($appuser->is_banned == 1):?><button class="btn btn-sm btn-danger unban" 
									appuserid='<?php echo $appuser->id;?>'><?php echo $this->lang->line('unban_label')?></button>
								<?php else:?><button class="btn btn-sm btn-primary ban" 
								   appuserid='<?php echo $appuser->id;?>'><?php echo $this->lang->line('ban_label')?></button><?php endif;?>
							</td>
							<?php endif;?>
						</tr>
						<?php
						endforeach;
					else:
				?>
						<tr>
							<td colspan='7'>
								<span class='glyphicon glyphicon-warning-sign menu-icon'></span>
								<?php echo $this->lang->line('no_appuser_data_message')?>
							</td>
						</tr>
				<?php
					endif;
				?>
			</table>
			</div>
			<?php 
				$this->pagination->initialize($pag);
				echo $this->pagination->create_links();
			?>

			<script>
			$(document).ready(function(){
				$(document).delegate('.ban','click',function(){
					var btn = $(this);
					var id = $(this).attr('appuserid');
					$.ajax({
						url: '<?php echo site_url('appusers/ban');?>/'+id,
						method:'GET',
						success:function(msg){
							if(msg == 'true')
								btn.addClass('unban').addClass('btn-danger').removeClass('btn-primary').removeClass('ban').html('Unban');
							else
								alert('System error occured. Please contact your system administrator.');
						}
					});
				});
				
				$(document).delegate('.unban','click',function(){
					var btn = $(this);
					var id = $(this).attr('appuserid');
					$.ajax({
						url: '<?php echo site_url('appusers/unban');?>/'+id,
						method:'GET',
						success:function(msg){
							if(msg == 'true')
								btn.addClass('ban').addClass('btn-primary').removeClass('btn-danger').removeClass('unban').html('Ban');
							else
								alert('System error occured. Please contact your system administrator.');
						}
					})
				});
			});
			</script>

