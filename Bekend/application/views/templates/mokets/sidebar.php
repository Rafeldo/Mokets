<div role="navigation">
    <div>
    	<ul class="nav" id="side-menu">
			
			<?php
				foreach($module_groups->result() as $grp){
					echo "<li>";
					echo "<a href='#'><i style='padding-right:3px;' class='fa ".$grp->group_icon." fa-fw'></i>".$grp->group_name. "<span class='fa arrow'></span></a>";
					
					?>
					<ul class="nav nav-second-level">
                        
                            <?php 
                            	foreach($allowed_modules->result() as $module){
                            		if($module->is_show_on_menu == 1 && $module->group_id == $grp->group_id && $grp->group_has_child == 1) {
                            			echo "<li style='padding-left: 10px;'>
                            			<a href='".site_url($module->module_name)."'><span class='fa fa-angle-right' style='padding-right: 4px;'></span>".
                            				$module->module_desc."</a></li>";
                            		}
                            	}
                            ?>
                        
                    </ul>
					<?php  
					
					echo "</li>";                          
				}
			?>
			
	
			</ul>

		</div>
</div>