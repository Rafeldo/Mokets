<?php
require_once(APPPATH.'/libraries/REST_Controller.php');

class Items extends REST_Controller
{
	function get_get()
	{
		$shop_id = $this->get('shop_id');
		if (!$shop_id) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
	
		$item_id = $this->get('id');
		$sub_cat_id = $this->get('sub_cat_id');
		if (!$item_id && !$sub_cat_id) {
			$this->response(array('error' => array('message' => 'require_item_id_or_sub_cat_id')));
		}
		
		if ($sub_cat_id) {
			$items = $this->get_items($shop_id, $sub_cat_id);
			
			$this->response(array(
				'status'=>'success',
				'data'	=>$items)
			);
		} else {
		
		
			$item = $this->item->get_info($item_id);
			$item->images = $this->image->get_all_by_type($item_id,"item")->result();
			$item->like_count = $this->like->count_all($shop_id, $item_id);
			$item->review_count = $this->review->count_all($shop_id, $item_id);
			$item->inquiries_count = $this->inquiry->count_all($shop_id, $item_id);
			$item->touches_count = $this->touch->count_all($shop_id, $item_id);
			
			$item->discount_name = $this->discount_type->get_info($item->discount_type_id)->name;
			$item->discount_percent = $this->discount_type->get_info($item->discount_type_id)->percent;
			
			$rating_value = $this->rating->get_rating_by_id($shop_id, $item_id)->total_rating;
			$count = $this->rating->count_all($shop_id, $item_id);
			if($rating_value != NULL) {
				//$item->rating_count = $rating_value / $count;
				$item->rating_count = floatval(number_format($rating_value / $count,2,".",""));
			} else {
				$item->rating_count = 0;
			}
			
			$shop = $this->shop->get_info($shop_id);
			$item->currency_symbol = html_entity_decode($shop->currency_symbol);
			$item->currency_short_form = html_entity_decode($shop->currency_short_form);
			$item->name = html_entity_decode($item->name);
			$item->description = html_entity_decode($item->description);
			
			$reviews = array();
			$j = 0;
			foreach ($this->review->get_all_by_item_id($item_id)->result() as $review) {
				$reviews[$j] = $review;
				$reviews[$j]->added = $this->ago($reviews[$j]->added);
				$appuser = $this->appuser->get_info($review->appuser_id);
				$reviews[$j]->appuser_name = $appuser->username;
				$reviews[$j++]->profile_photo = $appuser->profile_photo;
			}
			
			$attributes = array();
			$k = 0;
		
			foreach ($this->attribute_header->get_all_by_item_id($item_id)->result() as $header) {
				$attributes[$k] = $header;
				
				$detailNameStr = $this->get_attribute_detail_string($attributes[$k]->id,$shop_id);
				$detailNameOnly = "[".$detailNameStr."]";
				$attributes[$k]->detailString = $detailNameOnly;
				
				$attributes_detail = array();
				$l = 0;
				$detailStr = "";
				
				
				foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$shop_id)->result() as $detail) {
					
					//$detailStr .= "'" .$detail->name ."',";
					$attributes_detail[$l] = $detail;
					//$attributes_detail[$l]->detailString = $detailStr;
					$l++;
				}
				$attributes[$k]->details = $attributes_detail;
				
				
				
				
				$k++;
				
			}
			
			$item->reviews = $reviews;
			$item->attributes = $attributes;
			$attributes = null;
			//$attributes_detail = null;
			$this->response(array(
				'status'=>'success',
				'data'	=>$item)
			);
		}
	}
	
	function get_attribute_detail_string($header_id,$shop_id) 
	{
		foreach ($this->attribute_detail->get_all_by_header($header_id,$shop_id)->result() as $detail) {
			
			$detailStr .= "'" .$detail->name ."',";
			
		}
		
		return $detailStr;
	}
	
	function search_post()
	{
		$shop_id = $this->get('shop_id');
					
		$keyword = "";
		$data = $this->post();
		
		if($data['keyword']) {
			$keyword = $data['keyword'];
		} 
		
		
		$items = $this->item->get_all_by_search($shop_id, $keyword)->result();
		
		$i = 0;
		foreach ($items as $item) {
			$items[$i]->images = $this->image->get_all_by_type($item->id,"item")->result();
			$items[$i]->like_count = $this->like->count_all($shop_id, $item->id);
			$items[$i]->review_count = $this->review->count_all($shop_id, $item->id);
			$items[$i]->inquiries_count = $this->inquiry->count_all($shop_id, $item->id);
			$items[$i]->touches_count = $this->touch->count_all($shop_id, $item->id);
			$items[$i]->discount_name = $this->discount_type->get_info($item->discount_type_id)->name;
			$items[$i]->discount_percent = $this->discount_type->get_info($item->discount_type_id)->percent;
			
			$shop = $this->shop->get_info($shop_id);
			$item->currency_symbol = $shop->currency_symbol;
			$item->currency_short_form = $shop->currency_short_form;		
			
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
			
			
			foreach ($this->attribute_header->get_all_by_item_id($item->id)->result() as $header) {
				$attributes[$k] = $header;
				$detailNameStr = $this->get_attribute_detail_string($attributes[$k]->id,$shop_id);
				$detailNameOnly = "[".$detailNameStr."]";
				$attributes[$k]->detailString = $detailNameOnly;
				
				$attributes_detail = array();
				$l = 0;
				foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$attributes[$k]->shop_id)->result() as $detail) {
					$attributes_detail[$l++] = $detail;
				}
				$attributes[$k]->details = $attributes_detail;
				
				
				
				
				$k++;
				
			}
			
			$items[$i]->reviews = $reviews;
			$items[$i]->attributes = $attributes;
			$i++;
			$attributes_detail = null;
			$attributes = null;
			
		}
		
		//print_r($items);die;
		
		$this->response(array(
			'status'=>'success',
			'data'	=>$items)
		);
		
		
	}
	
	function get_items($shop_id, $sub_cat_id)
	{
		
		$all = $this->get('item');
		$count = $this->get('count');
		$from = $this->get('from');
		$keyword = "";
		$data = $this->post();
		
		/*
		if ($this->get('keyword')) {
			$keyword = $this->get('keyword');
		}
		*/
		if($data['keyword']) {
			$keyword = $data['keyword'];
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
			$items[$i]->images = $this->image->get_all_by_type($item->id,"item")->result();
			$items[$i]->like_count = $this->like->count_all($shop_id, $item->id);
			$items[$i]->review_count = $this->review->count_all($shop_id, $item->id);
			$items[$i]->inquiries_count = $this->inquiry->count_all($shop_id, $item->id);
			$items[$i]->touches_count = $this->touch->count_all($shop_id, $item->id);
			$items[$i]->discount_name = $this->discount_type->get_info($item->discount_type_id)->name;
			$items[$i]->discount_percent = $this->discount_type->get_info($item->discount_type_id)->percent;
			
			$shop = $this->shop->get_info($shop_id);
			$item->currency_symbol = html_entity_decode($shop->currency_symbol);
			$item->currency_short_form = html_entity_decode($shop->currency_short_form);
			$item->name = html_entity_decode($item->name);
			$item->description = html_entity_decode($item->description);
			
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
			
			
			foreach ($this->attribute_header->get_all_by_item_id($item->id)->result() as $header) {
				$attributes[$k] = $header;
				$detailNameStr = $this->get_attribute_detail_string($attributes[$k]->id,$shop_id);
				$detailNameOnly = "[".$detailNameStr."]";
				$attributes[$k]->detailString = $detailNameOnly;
				
				$attributes_detail = array();
				$l = 0;
				foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$attributes[$k]->shop_id)->result() as $detail) {
					$attributes_detail[$l++] = $detail;
				}
				$attributes[$k]->details = $attributes_detail;
				
				
				
				
				$k++;
				
			}
			
			$items[$i]->reviews = $reviews;
			$items[$i]->attributes = $attributes;
			$i++;
			$attributes_detail = null;
			$attributes = null;
		}
		
		return $items;
	}
	
	function reviews_count_get()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}
	
		$count = $this->like->count_all($item_id);
			
		$this->response(array('count' => $count));
	}
	
	function inquiries_count_get()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}
	
		$count = $this->inquiry->count_all($item_id);
			
		$this->response(array('count' => $count));
	}

	function likes_count_get()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}	
	
		$count = $this->like->count_all($item_id);
			
		$this->response(array('count' => $count));
	}
	
	function touches_count_get()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}
	
		$count = $this->touch->count_all($item_id);
			
		$this->response(array('count' => $count));
	}
	
	function review_post()
	{
		$item_id = $this->get('id');
		
		
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}
		
		$data = $this->post();
		$shop_id = $data['shop_id'];
		
		if ($data == null) {
			$this->response(array('error' => array('message' => 'invalid_json')));
		}
		
		if (!array_key_exists('appuser_id',$data)) {
			$this->response(array('error' => array('message' => 'require_appuser_id')));
		}
			
		if (!array_key_exists('review',$data)) {
			$this->response(array('error' => array('message' => 'require_review')));
		}
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		$data = array(
			'item_id' => $item_id,
			'appuser_id' => $data['appuser_id'],
			'review' => $data['review'],
			'shop_id' => $data['shop_id']
		);
		
		$this->review->save($data);
//		$count = $this->review->count_all($data['shop_id'], $item_id);
//		$this->response(array(
//			'success'=>'Review is saved successfully!',
//			'total'	=>$count
//		));
		
		$item = $this->item->get_info($item_id);
		$item->images = $this->image->get_all_by_type($item_id,"item")->result();
		$item->like_count = $this->like->count_all($data['shop_id'], $item_id);
		$item->review_count = $this->review->count_all($data['shop_id'], $item_id);
		$item->inquiries_count = $this->inquiry->count_all($data['shop_id'], $item_id);
		$item->touches_count = $this->touch->count_all($data['shop_id'], $item_id);
		
		$reviews = array();
		$j = 0;
		foreach ($this->review->get_all_by_item_id($item_id)->result() as $review) {
			$reviews[$j] = $review;
			$reviews[$j]->added = $this->ago($reviews[$j]->added);
			$appuser = $this->appuser->get_info($review->appuser_id);
			$reviews[$j]->appuser_name = $appuser->username;
			$reviews[$j++]->profile_photo = $appuser->profile_photo;
		}
		
		
		$item->discount_name = $this->discount_type->get_info($item->discount_type_id)->name;
		$item->discount_percent = $this->discount_type->get_info($item->discount_type_id)->percent;
		$rating_value = $this->rating->get_rating_by_id($shop_id, $item_id)->total_rating;
		$count = $this->rating->count_all($shop_id, $item_id);
		
		if($rating_value != NULL) {
			$item->rating_count = $rating_value / $count;
		} else {
			$item->rating_count = 0;
		}
		
		$shop = $this->shop->get_info($shop_id);
		$item->currency_symbol = $shop->currency_symbol;
		$item->currency_short_form = $shop->currency_short_form;
		
		$reviews = array();
		$j = 0;
		foreach ($this->review->get_all_by_item_id($item_id)->result() as $review) {
			$reviews[$j] = $review;
			$reviews[$j]->added = $this->ago($reviews[$j]->added);
			$appuser = $this->appuser->get_info($review->appuser_id);
			$reviews[$j]->appuser_name = $appuser->username;
			$reviews[$j++]->profile_photo = $appuser->profile_photo;
		}
		
		$attributes = array();
		
		$k = 0;
	
		foreach ($this->attribute_header->get_all_by_item_id($item_id)->result() as $header) {
			$attributes[$k] = $header;
			
			$detailNameStr = $this->get_attribute_detail_string($attributes[$k]->id,$shop_id);
			$detailNameOnly = "[".$detailNameStr."]";
			$attributes[$k]->detailString = $detailNameOnly;
			
			$attributes_detail = array();
			$l = 0;
			$detailStr = "";
			
			
			foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$shop_id)->result() as $detail) {
				
				//$detailStr .= "'" .$detail->name ."',";
				$attributes_detail[$l] = $detail;
				//$attributes_detail[$l]->detailString = $detailStr;
				$l++;
			}
			$attributes[$k]->details = $attributes_detail;
			
			
			
			
			$k++;
			
		}
		
		
		$item->reviews = $reviews;
		$item->attributes = $attributes;
		$attributes = null;
		
		
		
		$this->response(array(
			'status'=>'success',
			'data'	=>$item)
		);
		
	}
	
	function inquiry_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array('error' => array('message' => 'require_id')));
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
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array('error' => array('message' => 'require_shop_id')));
		}
		
		$data = array(
			'item_id' => $item_id,
			'name' => $data['name'],
			'email' => $data['email'],
			'message' => $data['message'],
			'shop_id' => $data['shop_id']
		);
		
		$this->inquiry->save($data);
		$this->response(array(
			'status'=>'success',
			'data'	=>'Inquiry is saved successfully!')
		);
	}
	
	function like_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		$platform_name = $this->post('platformName');
		if ( !$platform_name ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		
		
		if ($data == null) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required User ID')
			);
		}
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required Shop ID')
			);
		}
		
		
		
		$data = array(
			'item_id' => $item_id,
			'appuser_id' => $data['appuser_id'],
			'shop_id' => $data['shop_id']
		);
		
		if($platform_name == "ios") {
			//Change For iOS
			if ($this->like->exists(array('appuser_id' => $data['appuser_id'], 'item_id' => $item_id))) {
				$this->like->un_like($data);
				$count = $this->like->count_all($data['shop_id'], $item_id);
				$this->response(array(
					'status'=> 'unlike_success',
					'data'	=> $count)
				);
				
				
			} else {
				$this->like->save($data);
				$count = $this->like->count_all($data['shop_id'], $item_id);
				$this->response(array(
					'status'=> 'like_success',
					'data'	=> $count)
				);
			}
		
		} else {
			
			if ($this->like->exists(array('appuser_id' => $data['appuser_id'], 'item_id' => $item_id))) {
				$this->like->un_like($data);
				$count = $this->like->count_all($data['shop_id'], $item_id);
				$this->response(array(
					'status'=> 'success',
					'data'	=> $count)
				);
				
				
			} else {
				$this->like->save($data);
				$count = $this->like->count_all($data['shop_id'], $item_id);
				$this->response(array(
					'status'=> 'success',
					'data'	=> $count)
				);
			}
		
		}
		
	}
	
	function is_like_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		$platform_name = $this->post('platformName');
		if ( !$platform_name ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		if ($data == null) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required User ID')
			);
		}
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'Required Shop ID')
			);
		}		
		
		$count = $this->like->count_all($data['shop_id'], $item_id);
		
		if($platform_name == "ios") {
		
			if ($this->like->exists(array(
				'item_id' => $item_id,
				'appuser_id' => $data['appuser_id'],
				'shop_id' => $data['shop_id']))) {
				
				$this->response(array(
					'status'=> 'success',
					'data'	=> 'yes')
				);
				
			} else {
				$this->response(array(
					'status'=> 'success',
					'data'	=> 'no')
				);
			}
		
		} else {
			
			if ($this->like->exists(array(
				'item_id' => $item_id,
				'appuser_id' => $data['appuser_id'],
				'shop_id' => $data['shop_id']))) {
				//$this->response(array('status'=> 'yes','total'	=>$count));
				
				$this->response(array(
					'status'=> 'success',
					'data'	=> array('isLike'=> 'yes','total'	=>$count))
				);
				
			} else {
				//$this->response(array('status'=> 'no','total'	=>$count));
				
				$this->response(array(
					'status'=> 'success',
					'data'	=> array('isLike'=> 'no','total'	=>$count))
				);
			}
		
		}
		
	}
	
	function favourite_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		$platform_name = $this->post('platformName');
		if ( !$platform_name ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required User ID')
			);
		}
		
		if (!array_key_exists('shop_id', $data)) {
			
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Shop ID')
			);
		}
		
		$data = array(
			'item_id' => $item_id,
			'appuser_id' => $data['appuser_id'],
			'shop_id' => $data['shop_id']
		);
		
		if($platform_name == "ios") {
			
			if ($this->favourite->exists(array('appuser_id' => $data['appuser_id'], 'item_id' => $item_id))) {
				
				$this->favourite->un_favourite($data);
				$count = $this->favourite->count_all($data['shop_id'], $item_id);
				
				$this->response(array(
					'status'=>'unfavourite_success',
					'data'	=> $count)
				);
				
			} else {
			
				$this->favourite->save($data);
				$count = $this->favourite->count_all($data['shop_id'], $item_id);
				
				$this->response(array(
					'status'=>'favourite_success',
					'data'	=> $count)
				);
			
			}
		
		} else {
			
			if ($this->favourite->exists(array('appuser_id' => $data['appuser_id'], 'item_id' => $item_id))) {
						
				$this->favourite->un_favourite($data);
				$count = $this->favourite->count_all($data['shop_id'], $item_id);
				
				$this->response(array(
					'status'=>'success',
					'data'	=> $count)
				);
				
			} else {
			
				$this->favourite->save($data);
				$count = $this->favourite->count_all($data['shop_id'], $item_id);
				$this->response(array(
					'status'=>'success',
					'data'	=> $count)
				);
				
			
			}
		}	
	}
	
	function is_favourite_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required User ID')
			);
		}
				
		if ($this->favourite->exists(array(
			'item_id' => $item_id,
			'appuser_id' => $data['appuser_id'],
			'shop_id' => $data['shop_id']
			))) {
			
			$this->response(array(
				'status'=>'success',
				'data'	=> 'yes')
			);
		} else {
			$this->response(array(
				'status'=>'success',
				'data'	=> 'no')
			);
		}
		
	}
	
	function user_favourites_get()
	{
		
		$user_id = $this->get('user_id');
		$count = $this->get('count');
		$from = $this->get('from');
		
		if (!$user_id) {
			$this->response(array('error' => array('message' => 'require_id')));
		}
	
		if ($count && $from) {
			$favourites = $this->favourite->get_by_user_id($user_id, $count, $from)->result();
		} else if ($count) {
			$favourites = $this->favourite->get_by_user_id($user_id, $count)->result();
		} else {
			$favourites = $this->favourite->get_by_user_id($user_id)->result();
		}
		
		
		if(count($favourites)>0){
			$data = array();
			foreach ($favourites as $favourite) {
				$this->get_favourite_detail($favourite);
				$data[] = $favourite->item;
			}
			$this->response(array(
				'status'=>'success',
				'data'	=>$data)
			);
		} else {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'No Favourite Item')
			);
		}
	}
	
	function get_favourite_detail(&$favourite)
	{
		$item_id = $favourite->item_id;
		$item = $this->item->get_info($item_id);
		$item->name = html_entity_decode($item->name);
		$item->description = html_entity_decode($item->description);
		$item->cat_name = $this->category->get_cat_name_by_id($item->cat_id);
		$item->images = $this->image->get_all_by_type($item_id,"item")->result();
		$item->like_count = $this->like->count_all($item->shop_id, $item_id);
		$item->review_count = $this->review->count_all($item->shop_id, $item_id);
		$item->inquiries_count = $this->inquiry->count_all($item->shop_id, $item_id);
		$item->touches_count = $this->touch->count_all($item->shop_id, $item_id);
		
		$item->discount_name = $this->discount_type->get_info($item->discount_type_id)->name;
		$item->discount_percent = $this->discount_type->get_info($item->discount_type_id)->percent;
		
		$rating_value = $this->rating->get_rating_by_id($item->shop_id, $item_id)->total_rating;
		$count = $this->rating->count_all($item->shop_id, $item_id);
		if($rating_value != NULL) {
			$item->rating_count = $rating_value / $count;
		} else {
			$item->rating_count = 0;
		}
		
		$shop = $this->shop->get_info($item->shop_id);
		$item->currency_symbol = $shop->currency_symbol;
		$item->currency_short_form = $shop->currency_short_form;
		
		$reviews = array();
		$j = 0;
		foreach ($this->review->get_all_by_item_id($item_id)->result() as $review) {
			$reviews[$j] = $review;
			$reviews[$j]->added = $this->ago($reviews[$j]->added);
			$appuser = $this->appuser->get_info($review->appuser_id);
			$reviews[$j]->appuser_name = $appuser->username;
			$reviews[$j++]->profile_photo = $appuser->profile_photo;
		}
		
		
		///
		
		$attributes = array();
		$k = 0;
		
		
		foreach ($this->attribute_header->get_all_by_item_id($item->id)->result() as $header) {
			$attributes[$k] = $header;
			$detailNameStr = $this->get_attribute_detail_string($attributes[$k]->id,$shop_id);
			$detailNameOnly = "[".$detailNameStr."]";
			$attributes[$k]->detailString = $detailNameOnly;
			
			$attributes_detail = array();
			$l = 0;
			foreach ($this->attribute_detail->get_all_by_header($attributes[$k]->id,$attributes[$k]->shop_id)->result() as $detail) {
				$attributes_detail[$l++] = $detail;
			}
			$attributes[$k]->details = $attributes_detail;
			
			
			
			
			$k++;
			
		}
		$item->reviews = $reviews;
		$item->attributes = $attributes;
		
		$attributes_detail = null;
		$attributes = null;
		
		
		$favourite->item = $item;
	}
	
	function popular_items_get()
	{
		$count = $this->get('count');
		$from = $this->get('from');
	
		$popularItems = array();
		if ($count && $from) {
			$popularItems = $this->item->get_popular_items($count, $from)->result();
		} else if ($count) {
			$popularItems = $this->item->get_popular_items($count)->result();
		} else {
			$popularItems = $this->item->get_popular_items()->result();
		}
		
		$data = array();
		foreach ($popularItems as $popular) {
			$this->get_popular_detail($popular);
			$data[] = $popular;
		}
		$this->response($data);
		
	}
	
	function get_popular_detail(&$popular)
	{
		$item_id = $popular->item_id;
		$item = $this->item->get_info($item_id);
		$item->cat_name = $this->category->get_cat_name_by_id($item->cat_id);
		$item->images = $this->image->get_all_by_type($item_id,"item")->result();
		$item->like_count = $this->like->count_all($item_id);
		$item->unlike_count = $this->unlike->count_all($item_id);
		$item->review_count = $this->review->count_all($item_id);
		$item->inquiries_count = $this->inquiry->count_all($item_id);
		$item->touches_count = $this->touch->count_all($item_id);
		$item->popular_count = $popular->cnt;
		
		$reviews = array();
		$j = 0;
		foreach ($this->review->get_all_by_item_id($item_id)->result() as $review) {
			$reviews[$j] = $review;
			$reviews[$j]->added = $this->ago($reviews[$j]->added);
			$appuser = $this->appuser->get_info($review->appuser_id);
			$reviews[$j]->appuser_name = $appuser->username;
			$reviews[$j++]->profile_photo = $appuser->profile_photo;
		}
		
		$item->reviews = $reviews;
		$popular->item = $item;
	}
	
	function touch_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id',$data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required User ID')
			);
		}
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Shop ID')
			);
		}
		
		$data = array(
			'item_id' => $item_id,
			'appuser_id' => $data['appuser_id'],
			'shop_id' => $data['shop_id']
		);
		
		$this->touch->save($data);
		$count = $this->touch->count_all($data['shop_id'], $item_id);
		
		$this->response(array(
			'status'=>'success',
			'data'	=> $count)
		);
	}
	
	function rating_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required ID')
			);
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
		if (!array_key_exists('appuser_id',$data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required User Id')
			);
		}
		
		if (!array_key_exists('shop_id', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Shop Id')
			);
		}
		
		$data = array(
			'item_id'    => $item_id,
			'shop_id'    => $data['shop_id'],
			'appuser_id' => $data['appuser_id'],
			'rating'     => $data['rating']
		);
		
		$this->rating->save($data);
		
		$rating_value = $this->rating->get_rating_by_id($data['shop_id'], $item_id)->total_rating;
		$count = $this->rating->count_all($data['shop_id'], $item_id);
		//$item_rating_value = $rating_value / $count;
		
		$item_rating_value = number_format($rating_value / $count,2,".","");
		
		$this->response(array(
			'status'=>'success',
			'data'	=> $item_rating_value)
		);
	}
	
	function is_rate_post()
	{
		$item_id = $this->get('id');
		if (!$item_id) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Id')
			);
		}
		
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}
		
				
		if ($this->rating->exists(array(
			'item_id' => $item_id,
			'shop_id' => $data['shop_id']
			))) {
			
			$rating_value = $this->rating->get_rating_by_id($data['shop_id'], $item_id)->total_rating;
			$count = $this->rating->count_all($data['shop_id'], $item_id);
			$item_rating_value = $rating_value / $count;
			
			$this->response(array(
				'status'=> 'success',
				'data'	=> array('isRate'=> 'yes','total'	=>$item_rating_value))
			);
			
		} else {
			
			$this->response(array(
				'status'=> 'success',
				'data'	=> array('isRate'=> 'no','total'	=> 0 ))
			);
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
?>