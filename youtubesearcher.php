<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title><?php echo $_POST ? '検索結果一覧' : 'Youtube Searcher'; ?></title>
		
		<link rel="stylesheet" type="text/css" href="./css/youtubesearcher.css"/>
		<link rel="stylesheet" type="text/css" href="./css/validationEngine.jquery.css"/>		
		<script src="./js/jquery-2.1.3.min.js" type="text/javascript"></script>
		<script src="./js/jquery.validationEngine-ja.js" type="text/javascript" charset="utf-8"></script>
		<script src="./js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<script src="./js/youtubesearcher.js" type="text/javascript"></script>
	</head>
	<body>
	<img id="logo" src="./img/YouTube-logo-full_color.png" align="left"/>
	<h1 id="headline">
<?php
	if (!isset($_POST['submit'])) {
?>
	ようこそ！YoutubeSearcherへ！
<?php 
	}else{ 
?>
	検索結果
<?php } ?>
	</h1>
	<br clear="left" />
	<form method="post" id=search-form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		<select id="sort" name="sort" class="pointer">
			<option value="relevance">関連度の高い</option>
			<option value="viewCount">再生回数</option>
			<option value="rating">評価の高い</option>
		</select>
		<input type="text" class="textbox validate[required]" name="search_words" value="<?php if(isset($_POST['submit'])) echo htmlspecialchars($_POST['search_words']); ?>"/>
		
		<input type="submit" class="button pointer" name="submit" value="Search"/>
	  
	</form>
<?php
	if (!isset($_POST['submit'])) {
?>
	<div id="summary">
	<h4>YoutubeSearcherの概要</h4>
	このサイトは検索していただいたキーワードを元にYoutubeのAPIを用い、<br />
	動画を抽出してきています。<br />
	再生回数、評価などを元に独自の基準から質のいい動画のみを選定し、<br />
	探し出してくれるサービスを提供しています。<br />
	</div>
<?php      
	} else {
		if (!isset($_POST['search_words']) || empty($_POST['search_words'])) die ('エラー: 検索するキーワードが入力されていません。');
		require('youtubesearcher_func.php');
		action_search($_POST['search_words'], $_POST['sort']); 
		
?>
			<br />
			<?php if($search_params['resultsPerPage'] > 0): ?>
			<div id="player"></div><br />
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
			<span id="searchlst">検索結果一覧</span>
			<span id="movie-hit-cnt"><?php echo $search_params['totalResults'] != 1000000 ? :"1000000超の"; ?>動画中、<?php echo $search_params['resultsPerPage']; ?>件ヒットしました。</span>
			</span><br/>
			
			<?php $cnt = 0; foreach($response_params as $response_param){ ?>
				<a class='img-block' align="left">
				<img src="<?php echo $response_param['thumbnail']; ?>" class="img absolute pointer <?php if($cnt==0){echo 'border-trans'; $cnt++;} ?>" data-yt-id="<?php echo $response_param['id']; ?>"/><br />
				</a>
				<span class="img-params">
					<a href="<?php echo $response_param['url']; ?>" target="_blank" class="title pointer">
						<?php echo $response_param['title']; ?>
					</a>
				</span><br /><br />
				<span class="viewcnt img-params">視聴回数: <?php echo $response_param['view_cnt']; ?>回</span><br />
				<span class="video-time img-params">再生時間: <?php echo $response_param['video_time']; ?></span>
				<br clear="left" /><br />
			<?php } ?>
     		</div>
	<?php } ?>
	</body>
</html>

