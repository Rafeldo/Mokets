<?php 
require_once(APPPATH.'/libraries/REST_Controller.php');

class Shops extends REST_Controller
{
	function get_get()
	{
		$data = null;
		
		$id = $this->get('id');

		if ($id) {
			//echo "1";die;
			$shop = $this->shop->get_info($id);
			
			$shop->item_count = $this->item->count_all($id);
			$shop->category_count = $this->category->count_all_by($id);
			$shop->sub_category_count = $this->sub_category->count_all_by($id);
			$shop->follow_count = $this->follow->count_all($shop->id);
			$shop->cover_image_file = $this->image->get_cover_image($id,'shop')->path;
			$shop->cover_image_width = $this->image->get_cover_image($id,'shop')->width;
			$shop->cover_image_height = $this->image->get_cover_image($id,'shop')->height;
			$shop->cover_image_description = $this->image->get_cover_image($id,'shop')->description;
			
			$shop->categories = $this->get_categories($id); 
			$shop->currency_symbol = html_entity_decode($shop->currency_symbol);
			$shop->name = html_entity_decode($shop->name);
			$shop->description = html_entity_decode($shop->description);
			$shop->address = html_entity_decode($shop->address);	
			
			
			//$shop->currency = $this->currency->get_info(1);
			$data = $shop;
			
		} else {
			
			$shops = $this->shop->get_all()->result();
			foreach ($shops as $shop) {
				$shop->item_count = $this->item->count_all($shop->id);
				$shop->category_count = $this->category->count_all_by($shop->id);
				$shop->sub_category_count = $this->sub_category->count_all_by($shop->id);
				$shop->follow_count = $this->follow->count_all($shop->id);
				$shop->cover_image_file = $this->image->get_cover_image($shop->id,'shop')->path;
				$shop->cover_image_width = $this->image->get_cover_image($shop->id,'shop')->width;
				$shop->cover_image_height = $this->image->get_cover_image($shop->id,'shop')->height;
				$shop->cover_image_description = $this->image->get_cover_image($shop->id,'shop')->description;
				//$shop->currency = $this->currency->get_info(1);
				$shop->categories = $this->get_categories($shop->id);		
				$shop->currency_symbol = html_entity_decode($shop->currency_symbol);		
				$shop->name = html_entity_decode($shop->name);
				$shop->description = html_entity_decode($shop->description);
				$shop->address = html_entity_decode($shop->address);			
			}
			
			
			$data = $shops;
		}
		
		$this->response(array(
			'status'=>'success',
			'data'	=>$data)
		);
	}
	
	function get_categories($shop_id)
	{
		$cats = $this->category->get_only_publish($shop_id)->result();
		foreach ($cats as $cat) {
			$cat->cover_image_file = $this->image->get_cover_image($cat->id,'category')->path;
			$cat->cover_image_width = $this->image->get_cover_image($cat->id,'category')->width;
			$cat->cover_image_height = $this->image->get_cover_image($cat->id,'category')->height;
			$cat->name = html_entity_decode($cat->name);
			$cat->sub_categories = $this->get_sub_categories($shop_id, $cat->id);
		}
		
		return $cats;
	}
	
	function get_sub_categories($shop_id, $cat_id)
	{
		$sub_cats = $this->sub_category->get_all_by_cat_id($cat_id,"id","asc")->result();
		
		
		foreach ($sub_cats as $sub_cat) {
			//$sub_cat->items = $this->get_items($shop_id, $sub_cat->id);
			$sub_cat->cover_image_file   = $this->image->get_cover_image($sub_cat->id,'sub_category')->path;
			$sub_cat->cover_image_width  = $this->image->get_cover_image($sub_cat->id,'sub_category')->width;
			$sub_cat->cover_image_height = $this->image->get_cover_image($sub_cat->id,'sub_category')->height;
			$sub_cat->name = html_entity_decode($sub_cat->name);
		}
		
		
		return $sub_cats;
	}
	
	function get_items($shop_id, $sub_cat_id)
	{
		$all = $this->get('item');
		$count = $this->get('count');
		$from = $this->get('from');
		$keyword = "";
		if ($this->get('keyword')) {
			$keyword = $this->get('keyword');
		}
					
		if (!$all) {
			$items = $this->item->get_all_by_sub_cat($sub_cat_id, $keyword, 3)->result();
		} else {
			if ($count && $from) {
				$items = $this->item->get_all_by_sub_cat($sub_cat_id, $keyword, $count, $from)->result();
			} else if ($count) {
				$items = $this->item->get_all_by_sub_cat($sub_cat_id, $keyword, $count)->result();
			} else {
				$items = $this->item->get_all_by_sub_cat($sub_cat_id, $keyword)->result();
			}
		}
		
		$i = 0;
		foreach ($items as $item) {
			$items[$i]->images = $this->image->get_all_by_item($item->id)->result();
			$items[$i]->like_count = $this->like->count_all($shop_id, $item->id);
			$items[$i]->review_count = $this->review->count_all($shop_id, $item->id);
			$items[$i]->inquiries_count = $this->inquiry->count_all($shop_id, $item->id);
			$items[$i]->touches_count = $this->touch->count_all($shop_id, $item->id);
			
			$reviews = array();
			$j = 0;
			foreach ($this->review->get_all_by_item_id($item->id)->result() as $review) {
				$reviews[$j] = $review;
				$reviews[$j]->added = $this->ago($reviews[$j]->added);
				$appuser = $this->appuser->get_info($review->appuser_id);
				$reviews[$j]->appuser_name = $appuser->username;
				$reviews[$j++]->profile_photo = $appuser->profile_photo;
			}
			
			$items[$i++]->reviews = $reviews;
		}
		
		return $items;
	}
	
	function follow_post()
	{
		$shop_id = $this->get('shop_id');
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array('error' => array('message' => 'invalid_json')));
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array('error' => array('message' => 'require_appuser_id')));
		}
		
		if ($this->follow->exists(array('appuser_id' => $data['appuser_id'], 'shop_id' => $shop_id))) {
			$this->response(array('error' => array('message' => 'appuser_follow_exist')));
		}
		
		$data = array(
			'appuser_id' => $data['appuser_id'],
			'shop_id' => $shop_id
		);
		
		$this->follow->save($data);
		$count = $this->follow->count_all($shop_id);
		$this->response(array(
			'success'=>'Follow is saved successfully!',
			'total'	=>$count)
		);
	}
	
	
	
	function user_follows_get()
	{
		$user_id = $this->get('user_id');
		if (!$user_id) {
			$this->response(array('error' => array('message' => 'require_user_id')));
		}
		
		$follows = $this->follow->get_by_user_id($user_id)->result();
		if(count($follows)>0){
			$data = array();
			foreach ($follows as $follow) {
				$shop = $this->shop->get_info($follow->shop_id);
				$shop->item_count = $this->item->count_all($shop->id);
				$shop->category_count = $this->category->count_all_by($shop->id);
				$shop->follow_count = $this->follow->count_all($shop->id);
				$shop->feed_count = $this->feed->count_all($shop->id);
				$shop->cover_image_file = $this->image->get_cover_image($shop->id,'shop')->path;
				$shop->cover_image_width = $this->image->get_cover_image($shop->id,'shop')->width;
				$shop->cover_image_height = $this->image->get_cover_image($shop->id,'shop')->height;
				$shop->cover_image_description = $this->image->get_cover_image($shop->id,'shop')->description;
				$shop->feeds = $this->get_each_feed($shop->id);
				$data[] = $shop;
			}
			$this->response($data);
		} else {
			$this->response(array('error' => array('message' => 'no_follow_shops')));
		}
	}
	
	function get_each_feed($shop_id)
	{
		$feeds = $this->feed->get_all($shop_id)->result();
		foreach ($feeds as $feed) {
			$feed->id = $feed->id;
			$feed->title = html_entity_decode($feed->title);
			$feed->description = html_entity_decode($feed->description);
			$feed->added = $this->ago($feed->added);
			$feed->images = $this->image->get_all_by_type($feed->id,"feed")->result();
		}
		
		return $feeds;
	}
		
	function feeds_get()
	{
		$shop_id = $this->get('shop_id');
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
	
		$count = $this->get('count');
		$from = $this->get('from');
	
		$feeds = array();
		if ($count && $from) {
			$feeds = $this->feed->get_all($shop_id, $count, $from)->result();
		} else if ($count) {
			$feeds = $this->feed->get_all($shop_id, $count)->result();
		} else {
			$feeds = $this->feed->get_all($shop_id)->result();
		}
		
		$data = array();
		foreach ($feeds as $feed) {
			$feed->title = html_entity_decode($feed->title);
			$feed->description = html_entity_decode($feed->description);
			$this->get_feed_images($feed);
			$data[] = $feed;
		}
		
		$this->response(array(
			'status'=>'success',
			'data'	=> $data)
		);
		
	}
	
	function get_feed_images(&$feed)
	{
		$feed->added = $this->ago($feed->added);
		$feed->images = $this->image->get_all_by_type($feed->id, 'feed')->result();
	}
	
	function contactus_post()
	{
		$shop_id = $this->get('shop_id');
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array('error' => array('message' => 'invalid_json')));
		}
		
		if (!array_key_exists('name', $data)) {
			$this->response(array('error' => array('message' => 'require_name')));
		}
			
		if (!array_key_exists('email', $data)) {
			$this->response(array('error' => array('message' => 'require_email')));
		}
		
		if (!array_key_exists('message', $data)) {
			$this->response(array('error' => array('message' => 'require_message')));
		}
		
		$data = array(
			'name' => $data['name'],
			'email' => $data['email'],
			'message' => $data['message'],
			'shop_id' => $shop_id,
			'status' => 1
		);
		
		$this->contact->save($data);
		$this->response(array('success'=>'Contact message is saved successfully!'));
	}
	
	function is_follow_post() 
	{
		$shop_id = $this->get('shop_id');
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array('error' => array('message' => 'invalid_json')));
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array('error' => array('message' => 'require_appuser_id')));
		}
		
		if ($this->follow->exists(array('appuser_id' => $data['appuser_id'], 'shop_id' => $shop_id))) {
			$this->response(array('status'=> 'yes'));
		} else {
			$this->response(array('status'=> 'no'));
		}
		
	}

	function ago($time)
	{
		$time = mysql_to_unix($time);
		$now = mysql_to_unix($this->category->get_now());
		
	   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   $lengths = array("60","60","24","7","4.35","12","10");
	
	   $difference = $now - $time;
	  	$tense = "ago";
	
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