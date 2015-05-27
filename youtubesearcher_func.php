<?php
function action_search($search_words, $sort){
	//別ファイルで開く用
	global $search_params;
	global $response_params;
	//YouTubeAPI用parameter
	$APIKEY = 'AIzaSyBfOCQqZDS3BBq0ltQAVbm5XV5wwO_oiqI';
	$YouTubeAPIv3 ='https://www.googleapis.com/youtube/v3/';
	$maxResults = 20;
	$part_i = 'id';
	$part = 'id%2Csnippet%2Cstatistics%2CcontentDetails';
	$search_words = preg_replace("/(\s|　)/", "+",$search_words);
	$videoId = array();

	$feedURL1 = "{$YouTubeAPIv3}search?part={$part_i}&key={$APIKEY}&q={$search_words}&maxResults={$maxResults}";
	$ch = curl_init(); 
	curl_setopt_array($ch,
		array(
			CURLOPT_URL => $feedURL1,
			CURLOPT_SSL_VERIFYPEER =>false,
			CURLOPT_RETURNTRANSFER =>true,
		) 
	);

	$json1 = curl_exec($ch);
	curl_close($ch);
	
	//ini_set("allow_url_fopen",1);
	//$json1 = file_get_contents($feedURL1);
	
	$search_param_lst = json_decode( $json1 , true );
	$pageinfo = $search_param_lst['pageInfo'];
	$totalResults =  $pageinfo['totalResults'];
	
	$items_lst_temp = $search_param_lst['items'];
	foreach($items_lst_temp as $items_params_temp){
		(empty($items_params_temp['id']['videoId']) == true)?:$videoId[]=$items_params_temp['id']['videoId'] ;
	}
	
	$videoId_string = implode($videoId,',');
	$feedURL2 = "{$YouTubeAPIv3}videos?part={$part}&id={$videoId_string}&key={$APIKEY}";
	$json2 = file_get_contents($feedURL2, false);
	$movie_param_lst = json_decode( $json2 , true );
	
	$pageinfo = $movie_param_lst['pageInfo'];
 	$resultsPerPage = $pageinfo['resultsPerPage'];
	$search_params = array(
		'totalResults'	=>$totalResults,
		'resultsPerPage'=>$resultsPerPage,
		'search_words'	=>$search_words
	);
	
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
	/* ここで配列をソートしなおす
	if($sort == "viewCount" || $sort == rating){
		$response_params[]
	}*/
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
