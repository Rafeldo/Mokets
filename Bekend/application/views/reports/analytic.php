			<?php
			$this->lang->load('ps', 'english');
			$attributes = array('class' => 'form-inline','method' => 'POST');
			echo form_open(site_url('reports/analytic'), $attributes);
			?>
			  	<div class="form-group">
			  		<label><?php echo $this->lang->line('cat_label')?></label>
			  		<select class="form-control" name="cat_id" id="cat_id" >
			  		<option><?php echo $this->lang->line('select_cat_message')?></option>
			  		<?php 
			  		foreach($this->category->get_all($this->shop->get_current_shop()->id)->result() as $category){
			  			echo "<option value='".$category->id."'";
			  			if($cat_id == $category->id) echo " selected ";
			  			echo ">".$category->name."</option>";	
			  		}
			  		?>
			  		</select>
			  	</div>
			  	
			  	<div class="form-group">
			  		
			  		<label><?php echo $this->lang->line('sub_cat_label')?>
			  			
			  		</label>
			  		<select class="form-control" name="sub_cat_id" id="sub_cat_id">
			  			<option><?php echo $this->lang->line('select_sub_cat_message')?></option>
			  			<?php
			  				foreach($this->sub_category->get_all_by_cat_id($cat_id)->result() as $sub_cat){
			  					echo "<option value='".$sub_cat->id."'";
			  					if($sub_cat_id == $sub_cat->id) 
			  						echo " selected ";
			  					echo ">".$sub_cat->name."</option>";
			  				}
			  			?>
			  		</select>
			  	</div>
			  	
			  	<button type="submit" class="btn btn-primary">Generate Report</button>
			<?php echo form_close(); ?>
			<?php if($count > 0):?>
				<div id="chart_div" style="height: 500px;width: 800px;"></div>
				<div id="piechart" style="height: 400px;width: 700px;"></div>
			<?php endif;?>
			
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
				google.load("visualization", "1", {packages:["corechart"]});
				google.setOnLoadCallback(drawGraphChart);
				google.setOnLoadCallback(drawPieChart);
				
				function drawGraphChart() {
					
					var data = google.visualization.arrayToDataTable(<?php echo $graph_items;?>);
					var options = {
						title: 'Total Touch Counts (All Items From ' + '<?php echo $cat_name;?> and ' + '<?php echo $sub_cat_name;?>)',
						vAxis: {title: 'Items',  titleTextStyle: {color: 'red'}, minValue:0, maxValue:1000},
						colors:['#e57373'],
						backgroundColor: { fill:'transparent' }
					};
					
					var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
					chart.draw(data, options);
				}
				
				function drawPieChart() {
			     	
			     	var data = google.visualization.arrayToDataTable(<?php echo $pie_items;?>);
			     	var options = {
			       		title: 'Top 5 Popular Items From ' + '<?php echo $cat_name;?> and ' + '<?php echo $sub_cat_name;?>)',
			       		backgroundColor: { fill:'transparent' }
			     	};
			
			     	var chart = new google.visualization.PieChart(document.getElementById('piechart'));
			     	chart.draw(data, options);
			   }
			   
			   $('#cat_id').change(function(){
			   	var catId = $(this).val();
			   	$.ajax({
			   		url: '<?php echo site_url('items/get_sub_cats');?>/'+catId,
			   		method: 'GET',
			   		dataType: 'JSON',
			   		success:function(data){
			   			$('#sub_cat_id').html("");
			   			$.each(data, function(i, obj){
			   			    $('#sub_cat_id').append('<option value="'+ obj.id +'">' + obj.name + '</option>');
			   			});
			   			$('#name').val($('#name').val() + " ").blur();
			   		}
			   	});
			   });
			   
			   $('#sub_cat_id').on('change', function(){
			   	$('#name').val($('#name').val() + " ").blur();
			   });
			   
			</script>