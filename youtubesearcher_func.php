<?php
function action_search($search_words, $sort){
	//別ファイルで開く用
	global $search_params;
	global $response_params;
	
	//YouTubeAPI用parameter
	$APIKEY = 'AIzaSyBfOCQqZDS3BBq0ltQAVbm5XV5wwO_oiqI';
	$YouTubeAPIv3 ='https://www.googleapis.com/youtube/v3/';
	$maxResults = 10;
	$part_i = 'id';
	$part = 'id%2Csnippet%2Cstatistics%2CcontentDetails';
	
	$videoId = array();
	
	$feedURL1 = "{$YouTubeAPIv3}search?part={$part_i}&q={$search_words}&maxResults={$maxResults}&key={$APIKEY}";
	$json1 = file_get_contents($feedURL1,true);
	$search_param_lst = json_decode( $json1 , true );

	//var_dump($movie_param_lst);
	$pageinfo = $search_param_lst['pageInfo'];
	$totalResults =  $pageinfo['totalResults'];
 	$resultsPerPage = $pageinfo['resultsPerPage'];
	$search_params = array(
		'totalResults'	=>$totalResults,
		'resultsPerPage'=>$resultsPerPage,
		'search_words'	=>$search_words
	);
	
	$items_lst_temp = $search_param_lst['items'];
	foreach($items_lst_temp as $items_params_temp){
		(empty($items_params_temp['id']['videoId']) == true)?:$videoId[]=$items_params_temp['id']['videoId'] ;
	}
	
	$videoId_string = implode($videoId,',');
	$feedURL2 = "{$YouTubeAPIv3}videos?part={$part}&id={$videoId_string}&maxResults={$maxResults}&key={$APIKEY}";
	$json2 = file_get_contents($feedURL2, false);
	$movie_param_lst = json_decode( $json2 , true );
	
	$items_lst = $movie_param_lst['items'];
	foreach($items_lst as $item_params){
		$snippet    =	$item_params['snippet'];
		$contentD   =	$item_params['contentDetails'];
		$statistics =	$item_params['statistics'];
		//snippet群
		$id = $item_params['id'];
		$title = $snippet['title'];
		$published = $snippet['publishedAt'];
		$description = $snippet['description'];
		$thumbnail = $snippet['thumbnails']['medium']['url'];
		//contentDetails群
		$video_time = $contentD['duration'];	/*PT?H?M?S*/
		$replace_time = array('PT'=>'', 'H'=>':', 'M'=>':', 'S'=>'');
		$video_time = strtr($video_time, $replace_time);
		
		//statistics群
		$view_cnt = $statistics['viewCount'];
		$like_cnt = $statistics['likeCount'];
		$dislike_cnt = $statistics['dislikeCount'];
		
		$rating = ($dislike_cnt == 0)? $like_cnt : $like_cnt/$dislike_cnt;
		$url = "https://www.youtube.com/watch?v={$id}";
		if(evaluate_movie($totalResults, $view_cnt, $rating)){
			$response_params[] =array(
				'id'			=>$id,
				'title'			=>$title,
				'published'		=>$published,
				'description'	=>$description,
				'thumbnail'		=>$thumbnail,
				'video_time'	=>$video_time,
				'view_cnt'		=>$view_cnt,
				'rating'		=>$rating,
				'url'			=>$url
			);
		}
	}
	//こっからつづき
	
	/*
	foreach($movie_param_lst as $movie_params=>$val){
		
		if($movie_params == 'items'){
			foreach($val as $items_params){
				foreach($items_params as $item_params=>$aa){
				var_dump($item_params);
				//if($items_params == 'id'){
				//	foreach($items_params as $id_params=>$id_val){
				//		$movie_id = ($id_params == 'videoId')?:$id_val;
				//	}
				//}
				}
			}
			foreach($movie_items_params->snippet as $snippet_params){
				
			}
		}
	*/
		//動画の絞込み
		//if(evaluate_movie($total, $view_cnt, $rating)){
		//	$search_params[] =array(
		//		'description'	=>$description,
		//		'movie_seconds'	=>$movie_seconds,
		//		'url'			=>$url,
		//		'rating'		=>$rating,	
		//		'thumbnail_url'	=>$thumbnail_url,
		//		'title'			=>$title,
		//		'view_cnt'		=>$view_cnt,
		//		'author'		=>$author,
		//		'movie_id'		=>preg_replace('/.*v=([\d\w-]+).*/', '$1', $url)
		//	);
		//	$hit_cnt++;
		//}
		//var_dump($movie_id);
	//}
	
}

//動画を検索にヒットした動画数、再生回数、評価により判定する関数
//通ればtrue、通らなければfalseを返す
function evaluate_movie($totalResults, $view_cnt, $rating){
	$judge = ($rating >= 2)? true : false;

	if($judge){
		switch (true) {
			case ($totalResults > 10000 && $view_cnt > 30000):
				break;
			case ($totalResults > 1000  && $view_cnt > 10000):
				break;
			case ($totalResults > 100   && $view_cnt > 4000):
				break;
			case ($totalResults > 10    && $view_cnt > 1000):
				break;
			default:
				$judge = false;
				break;
		}
	}
	return $judge;
}
?>
