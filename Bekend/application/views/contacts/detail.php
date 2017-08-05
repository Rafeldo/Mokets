		<ul class="breadcrumb">
			<li>
				<a href="<?php echo site_url();?>">Dashboard</a> 
				<span class="divider"></span>
			</li>
			<li>Contact Us Information</li>
		</ul>
		<div class="wrapper wrapper-content animated fadeInRight">
			<div class="row">
				<div class="col-sm-6">
					<legend>Contact Us Information</legend>
					<table class="table table-striped table-bordered">
						
						<tr>
							<th>Name</th>
							<td><?php echo $contact->name;?></td>
						</tr>
						<tr>
							<th>Email</th>
							<td><?php echo $contact->email;?></td>
						</tr>
						<tr>
							<th>Message</th>
							<td><?php echo $contact->message;?></td>
						</tr>
					</table>
				</div>
			</div>
				
			<a class="btn btn-primary" href="<?php echo site_url('contacts');?>" class="btn">Back</a>
		</div>