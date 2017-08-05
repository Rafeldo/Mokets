		<ul class="breadcrumb">
			<li>
				<a href="<?php echo site_url();?>">Dashboard</a> 
				<span class="divider"></span>
			</li>
			<li>Contact Us List</li>
		</ul>
		
		
		<?php
		$attributes = array('class' => 'form-inline','method' => 'POST');
		echo form_open(site_url('contacts/search'), $attributes);
		?>
			<div class="form-group">

				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => '',
					'class' => 'form-control',
					'placeholder' => 'Search'
				)); ?>

		  	</div>
		  	<button type="submit" class="btn btn-default">Search</button>
		
		<?php echo form_close(); ?>
		
		<br/>
		
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
		<div class="wrapper wrapper-content animated fadeInRight">
			<table class="table table-striped table-bordered">
				<tr>
					<th>No</th>
					<th>Name</th>
					<th>Date</th>
					<th>Detail</th>
					<?php if(in_array('delete',$allowed_accesses)):?>
					<th>Delete</th>
					<?php endif;?>
				</tr>
				<?php
					if(!$count=$this->uri->segment(3))
						$count = 0;
					if(isset($contacts) && count($contacts->result())>0):
						foreach($contacts->result() as $contact):					
				?>
				<tr>
					<td><?php echo ++$count;?></td>
					<td><?php echo $contact->name;?></td>
					<td><?php echo $contact->added;?></td>
					<td><a href='<?php echo site_url('contacts/detail/'.$contact->id);?>'>Detail</a></td>
					
					<?php if(in_array('delete',$allowed_accesses)):?>
					<td><a href='<?php echo site_url("contacts/delete/".$contact->id);?>'><i class='glyphicon glyphicon-trash'></i></a></td>
					<?php endif;?>
				</tr>
					<?php
					endforeach;
					else:
					?>
				<tr>
					<td colspan='7'>There is no data.</td>
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
