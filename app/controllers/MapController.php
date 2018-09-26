<?php
class MapController extends BaseController {
	public function index()
	{       
		$feed = new SimplePie();
		//read data from feed
		$feed->set_feed_url(array('https://spreadsheets.google.com/feeds/list/0Ai2EnLApq68edEVRNU0xdW9QX1BqQXhHRl9sWDNfQXc/od6/public/basic'));
		$feed->init();
		$feed->handle_content_type();
		$messages = [];
		$maxDate=0;
		$minDate=INF;
		//prepare data 
		foreach($feed->get_items() as $item){
			$message =[];
			$messageParts = explode(',',$item->get_content());
			foreach($messageParts as $part){
				$part = explode(':',$part);
				if(count($part)<2)continue;
				$message[trim($part[0])]=trim($part[1]);
			}
			$message['title']=$item->get_title();
			$message['color']= $message['sentiment'] == "Positive"? "green" : ($message['sentiment'] == "Negative"? "red" : "blue");
			$d = DateTime::createFromFormat("y-d-m H:i",$message['title']);
			$message['date']=$d->format('Y-m-d H:i');
			$maxDate =  $message['date'] > $maxDate ?  $message['date'] : $maxDate ;
			$minDate =  $message['date'] < $minDate ?  $message['date'] : $minDate ;
			$messages[]=$message;
		}
		//handle opacity to view date
		$datediff = strtotime($maxDate)- strtotime($minDate);
		foreach($messages as $key=> $item ){
			$diff = strtotime($item['date'])- strtotime($minDate);
			$opacity =  $diff / $datediff ;
			$opacity= $opacity <0.3  ? 0.3 : $opacity;
			$messages[$key]['opacity']= $opacity;
		}
		return View::make('map/index')->with('messages',$messages);
	}
}
