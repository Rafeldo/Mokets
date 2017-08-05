<div class="navbar navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
		</div>
		
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li>
					<?php 
						if ($this->session->userdata('shop_id') != "") {
							if ($mode) {
					?>
								<a href="<?php echo site_url("dashboard") ?>">
										<?php echo "Dashboard";?>
										<span class='glyphicon glyphicon-home menu-icon'></span>
								</a>
					<?php
							} else {
					?>
								<a  href="<?php echo site_url("shops/edit/" . $this->shop->get_current_shop()->id) ?>">
										<?php echo $this->shop->get_current_shop()->name;?>
										<span class='glyphicon glyphicon-edit menu-icon'></span>
								</a>
					<?php
							}
						}
					?>
				</li>
				<?php 
					if(!$this->session->userdata('is_shop_admin')) {
				?>
				<li>
					<a href="<?php echo site_url("shops") ?>">
						Switch Shops
						<span class='glyphicon glyphicon-transfer menu-icon'></span>
					</a>
				</li>
				<?php } ?>
				<!--
				<li>
					<a href="<?php echo site_url("currencies") ?>">
						Currency Update
						<span class='glyphicon glyphicon-cog menu-icon'></span>
					</a>
				</li>
				-->
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Account
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li>
							<div class="navbar-content">
								<div class="row">
									<div class="col-md-5">
										<img src="<?php echo base_url('img/fokhwar.png');?>" alt="Alternate Text" class="img-responsive"/>
										<!--<p class="text-center small">
											<a href="#">Change Photo</a>
										</p>-->
									</div>
									<div class="col-md-7">
										<?php $logged_in_user = $this->user->get_logged_in_user_info();?>
										<span><?php echo $logged_in_user->user_name;?></span>
										<p class="text-muted small"><?php echo $this->role->get_name($logged_in_user->role_id);?></p>
									</div>
								</div>
							</div>
							<div class="navbar-footer">
								<div class="navbar-footer-content">
									<div class="row">
										<div class="col-md-6">
											<a href="<?php echo site_url('profile');?>" class="btn btn-default btn-sm" style="background-color: #fff;background-image:  none;border-radius: 0;">Edit Profile</a>
										</div>
										<div class="col-md-6">
											<a href="<?php echo site_url('logout');?>" class="btn btn-default btn-sm pull-right" style="background-color: #fff; border-radius: 0;">Sign Out</a>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>