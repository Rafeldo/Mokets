<?php

class Common {
	
	public function date_formatting($date=null) {
		
		$tmp = explode(" ",$date);
		$date_tmp = $tmp[0];
		
		$date_arr = explode("-", $tmp[0]);
		$year = $date_arr[0];
		$month = $date_arr[1];
		$day = $date_arr[2];
		
		switch ($month) {
		    case "01":
		        $month_str = "Jan";
		        break;
		    case "02":
		         $month_str = "Feb";
		        break;
		    case "03":
		        $month_str = "Mar";
		        break;
		    case "04":
		        $month_str = "Apr";
		        break;
		    case "05":
		        $month_str = "May";
		        break;
		    case "06":
		        $month_str = "Jun";
		        break;
		    case "07":
		        $month_str = "Jul";
		        break;
		    case "08":
		        $month_str = "Aug";
		        break;
		    case "09":
		        $month_str = "Sep";
		        break;
		    case "10":
		        $month_str = "Oct";
		        break;
		    case "11":
		        $month_str = "Nov";
		        break;
		    case "12":
		        $month_str = "Dec";
		        break;
		    default:
		         $month_str = "";
		}
		
		 
		return $day . " " . $month_str . " " . $year;
	}	
}