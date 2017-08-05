<?php 
require_once(APPPATH.'/libraries/REST_Controller.php');

class Categories extends REST_Controller
{
	function get_get()
	{
		$data = null;
		
		$id = $this->get('id');

		if ($id) {
			$cat = $this->category->get_info($id);
			$cat->items = $this->get_items($cat->id);
			$data = $cat;
		} else {
			$cats = $this->category->get_only_publish()->result();
			foreach ($cats as $cat) {
				$cat->items = $this->get_items($cat->id);
			}
			$data = $cats;
		}
		
		$this->response($data);
	}
	
	function get_items($cat_id)
	{
		$all = $this->get('item');
		$count = $this->get('count');
		$from = $this->get('from');
		$keyword = "";
		if ($this->get('keyword')) {
			$keyword = $this->get('keyword');
		}
					
		if (!$all) {
			$items = $this->item->get_all_by_cat($cat_id, $keyword, 3)->result();
		} else {
			if ($count && $from) {
				$items = $this->item->get_all_by_cat($cat_id, $keyword, $count, $from)->result();
			} else if ($count) {
				$items = $this->item->get_all_by_cat($cat_id, $keyword, $count)->result();
			} else {
				$items = $this->item->get_all_by_cat($cat_id, $keyword)->result();
			}
		}
		
		$i = 0;
		foreach ($items as $item) {
			$items[$i]->images = $this->image->get_all_by_item($item->id)->result();
			$items[$i]->like_count = $this->like->count_all($item->id);
			$items[$i]->unlike_count = $this->unlike->count_all($item->id);
			$items[$i]->review_count = $this->review->count_all($item->id);
			$items[$i]->inquiries_count = $this->inquiry->count_all($item->id);
			$items[$i]->touches_count = $this->touch->count_all($item->id);
			
			$reviews = array();
			$j = 0;
			foreach ($this->review->get_all_by_item_id($item->id)->result() as $review) {
				$reviews[$j] = $review;
				$reviews[$j]->added = $this->ago($reviews[$j]->added);
				$appuser = $this->appuser->get_info($review->appuser_id);
				$reviews[$j]->appuser_name = $appuser->username;
				$reviews[$j++]->profile_photo = $appuser->profile_photo;
			}
			
			$attributes = array();
			$k = 0;
			
			$attributes_detail = array();
			$l = 0;
			
			foreach ($this->attribute_header->get_all_by_item_id($item->id)->result() as $header) {
				$attributes[$k] = $header;
				
				foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$attributes[$k]->shop_id)->result() as $detail) {
					$attributes_detail[$l++] = $detail;
				}
				$attributes[$k]->details = $attributes_detail;
				$k++;
				
			}
			
			$items[$i]->reviews = $reviews;
			$items[$i]->attributes = $attributes;
			$i++;
		}
		
		return $items;
	}
	
	function get_subcategories_get()
	{
		$cat_id = $this->get('cat_id');
		$shop_id = $this->get('shop_id');
		
		$count = $this->get('count');
		$from = $this->get('from');
		
		if (!$cat_id) {
			$this->response(array('error' => array('message' => 'require_category_id')));
		}
		
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		
		$this->response($this->get_sub_categories($shop_id, $cat_id,$count,$from));
		
	}
	
	function get_sub_categories($shop_id, $cat_id,$count,$from)
	{
		
		
		if ($count && $from) {
			$sub_cats = $this->sub_category->get_all_by_cat_id($cat_id,"id","asc",$count, $from)->result();
		} else if ($count) { 
			$sub_cats = $this->sub_category->get_all_by_cat_id($cat_id,"id","asc",$count)->result();
		} else {
			$sub_cats = $this->sub_category->get_all_by_cat_id($cat_id,"id","asc")->result();
		}
		
		
		
		
		foreach ($sub_cats as $sub_cat) {
			//$sub_cat->items = $this->get_items($shop_id, $sub_cat->id);
			$sub_cat->cover_image_file   = $this->image->get_cover_image($sub_cat->id,'sub_category')->path;
			$sub_cat->cover_image_width  = $this->image->get_cover_image($sub_cat->id,'sub_category')->width;
			$sub_cat->cover_image_height = $this->image->get_cover_image($sub_cat->id,'sub_category')->height;
		}
		
		
		return $sub_cats;
	}
	
	function ago($time)
	{
		$time = mysql_to_unix($time);
		$now = mysql_to_unix($this->category->get_now());
		
	   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   $lengths = array("60","60","24","7","4.35","12","10");
	
	   $difference     = $now - $time;
	   $tense         = "ago";
	
	   for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	       $difference /= $lengths[$j];
	   }
	
	   $difference = round($difference);
	
	   if ($difference != 1) {
	       $periods[$j].= "s";
	   }
	   
	   if ($difference==0) {
	   		return "Just Now";
	   } else {
	   		return "$difference $periods[$j] ago";
	   }
	}
}
?>