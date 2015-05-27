<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="keywords" content="youtubesearcher,ユーチューブサーチャー,ようつべせあｒちぇｒ,動画,検索,youtube">
		<meta name="description" content="YouTubeSearcherは検索していただいたキーワードを元にYoutubeのAPIを用い、
				動画を抽出してきています。再生回数、評価などを元に独自の基準からあなたにオススメの動画を探しだすサービス
				を提供します。">
		<meta content="width=device-width, maximum-scale= 1" name="viewport">
		
		<title><?php echo $_POST ? '検索結果一覧' : 'Youtube Searcher'; ?></title>
		
		<link rel="stylesheet" type="text/css" href="./css/youtubesearcher.css"/>
		<link rel="stylesheet" type="text/css" href="./css/jquery.mCustomScrollbar.css"/>
		
		<script src="./js/jquery-2.1.3.min.js" type="text/javascript"></script>
		<script src="./js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
		<script src="./js/youtubesearcher.js" type="text/javascript"></script>
		<script src="./js/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>
	</head>
	<body>
	<div id="wrap">
	<div id="header">
	<!--<div id="footer">Copyright © YouTubeSearcher All Rights Reserved. presented by <a href="https://twitter.com/FSyuu" target="_blank">FSyuu</a></div>
	-->
	<a href="">
		<img id="icon" class="pointer" src="./img/yuu-icon.png" align="left" border="0"/>
	</a>
	
	<form method="post" action="" onSubmit="return validate();">
		<div  id="search-form">
		<select id="sort" name="sort" class="pointer">
			<option value="relevance">関連度の高い</option>
			<option value="viewCount">再生回数</option>
			<option value="rating">評価の高い</option>
		</select>
		<input type="text" id="textbox" class="textbox" name="search_words" value="<?php isset($_POST['submit']) ==""? : print(htmlspecialchars($_POST['search_words'])); ?>"/>
		<input type="submit" id="button" class="button pointer" name="submit" value="Search"/>
		
		<div id="fav-list-block">
		<div id="fav-list-button" class="pointer">お気に入り一覧</div>
		<div id="fav-list"></div></div></div>
		</form>
	</div>
	<br clear="left" />
	<br>
	<div id="main-contents">
	<h2 id="headline">
	<?php
		if (!isset($_POST['submit'])) {
	?>
		ようこそ！YoutubeSearcherへ！
		
	<?php 
		}else{ 
	?>
		検索結果
	<?php } ?>
		</h2>
		
	<?php
		if (!isset($_POST['submit'])) {
	?>
	<div class="summary">
	<h4>YoutubeSearcherの概要</h4>
	このサイトは検索していただいたキーワードを元にYoutubeのAPIを用い、<br />
	動画を抽出してきています。<br />
	再生回数、評価などを元に独自の基準からあなたにオススメの動画を<br />
	探しだすサービスを提供します。<br />
	</div>
	
	<div class="summary">
	<h4>お知らせ</h4>
	今後実装する予定の機能<br />
	・お気に入り機能<br />
	・動画のさらに読み込みボタン<br />
	・スマホでの動画閲覧
	</div>
	
<?php      
	} else {
		if (!isset($_POST['search_words']) || empty($_POST['search_words'])) die ('エラー: 検索するキーワードが入力されていません。');
		require('youtubesearcher_func.php');
		action_search($_POST['search_words'], $_POST['sort']); 
		
?>
			<br />
			<?php if($search_params['totalResults'] > 0): ?>
			<div id="player"></div><br clear="all"/>
			<div id="favorite-box">
			<div id="favorite-add" class="pointer favorite" thumb="" value="">
				<span></span>
			</div>
			<a href="https://twitter.com/share" class="twitter-share-button" 
			data-text="YouTubeSearcherで「<?php echo $_POST['search_words'];?>」って調べてみました！ページリロードしなくてきもちー！" data-via="FSyuu" data-lang="ja"
			data-hashtags="YouTubeSearcher" id="tweets">ツイート</a>
			<div class="fb-like" id="fav" data-href="http://web.chobi.net/~tyuuki1212/youtubesearcher.php" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
			</div>
			<?php else: ?>
			<span id="no-movie">動画が見つかりませんでした。</span>
			<?php endif; ?>
			<div id="how-to-use">
			<pre>
		<h4>この画面の利用方法</h4>
		<ul><li>検索ボックス
	ここには今検索した内容が書いてあります。
	違う内容で検索しなおすことも可能です。
						
		<li>画面中央のプレイヤー
	Youtubeの埋め込みプレイヤーです。
	デフォルトは検索してでてきた一番最初の動画となっています。
	切り替えることで他の動画を見れるようになります。(切り替え方法は後述)

		<li>右端の検索結果一覧
	ここには検索した内容の一覧が表示されています。
	動画ごとにサムネ、タイトル、再生回数、作成者が表示されています。
	サムネをクリックするとプレイヤーの動画が切り替わります。
	タイトルにはYoutubeの本家にリンクが貼られています。
		</ul>			
			</pre>		
			</div>
			<span id="imglst" class="absolute">
			<div class="gray5all">
			<span id="searchlst" class="nowrap">検索結果一覧</span>
			<?php if($search_params['totalResults'] >=1): ?>
			<span id="movie-hit-cnt" class="nowrap"><?php echo $search_params['totalResults'] != 1000000 ? $search_params['totalResults']:"1000000超の"; ?>
			動画中、<?php echo $search_params['resultsPerPage']; ?>件抽出しました。</span>
			</span><br/>
			<?php $cnt = 0; foreach($response_params as $response_param){ ?>
				<a class='img-block' align="left">
				<img src="<?php echo $response_param['thumbnail']; ?>" class="img absolute pointer <?php if($cnt==0){echo 'border-trans'; $cnt++;} ?>" data-yt-id="<?php echo $response_param['id']; ?>"/>
				</a>
				<span class="img-params">
					<a href="<?php echo $response_param['url']; ?>" target="_blank" class="title pointer">
						<?php echo $response_param['title']; ?>
					</a>
				</span><br />
				<span class="viewcnt img-params">視聴回数: <?php echo $response_param['view_cnt']; ?>回</span><br />
				<span class="video-time img-params">再生時間: <?php echo $response_param['video_time']; ?></span>
				<br clear="left" /><br / style="line-height:170%">
			<?php } else:?>
			<span id="movie-hit-cnt">ヒットした動画はありませんでした。</span>
			<?php endif; ?>
     		</div><br clear="right"/><br />
			
	<?php } ?>
	
	<p class="pagetop"><a href="#wrap">▲</a></p>
	</div>
	</div>
	
	</body>
</html>