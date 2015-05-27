$(function(){
	var player;
	
	/* IFrame Player APIのコードをロード */
	function fGetScript(){
		$.ajax({
			url:"http://www.youtube.com/player_api/",
			dataType:"script",
			success:function(data){
				dbg("done");
				/* プレーヤーの準備完了時 */
				window.onYouTubeIframeAPIReady=function() {
					dbg("onYouTubeIframeAPIReady");
					var first=$(".img");
					first.css("font-weight","bold");
					loadPlayer(first.attr("data-yt-id"), first.attr("src"));
					favList();
				}
			},
			error:function(xhr, status, thrown) {
				dbg(xhr);
				fGetScript();
			}
		}); 
	}
	fGetScript();
	/* プレーヤー生成 */
	function loadPlayer(videoId, thumb) {
		dbg("loadPlayer("+videoId+")");
		/* 埋め込むオブジェクトを生成（すでにある場合は削除）*/
		if(!player){
			player = new YT.Player(
				'player',{
					width: '820',   /* 動画プレーヤーの幅 */
					height: '500',   /* 動画プレーヤーの高さ */
					videoId: videoId,   /* YouTube動画ID */
					events: { /* イベント */
						"onReady": onPlayerReady   /* プレーヤの準備完了時 */
					}
				}
			);
		}else{
			player.loadVideoById(videoId); /* 指定した動画を読み込んで再生（自動再生扱い） */
		}
		favorite(videoId, thumb);
	}
	function onPlayerReady(event){
		dbg("onPlayerReady");
		event.target.setVolume(50);	/* 音量調整 */
		//event.target.playVideo();	/* 動画再生 */
	}
	/* 指定した動画を再生 */
	$(document).on('click', '.img', (function(){
		loadPlayer($(this).attr("data-yt-id"), $(this).attr("src"));
		$(".img").removeClass("border-trans");
		$(this).addClass("border-trans");
	}));
	
	function dbg(str){
		if(window.console && window.console.log){
			console.log(str);
		}
	}
	
	!function(d,s,id){
		var js,fjs=d.getElementsByTagName(s)[0],
		p=/^http:/.test(d.location)?'http':'https';
		if(!d.getElementById(id)){
			js=d.createElement(s);
			js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
			fjs.parentNode.insertBefore(js,fjs);
		}
	}
	(document, 'script', 'twitter-wjs');
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.3";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	
	$(document).ready(function() {
		var pagetop = $('.pagetop');
		$(window).scroll(function () {
			if ($(this).scrollTop() > 500) {
				pagetop.fadeIn();
			} else {
				pagetop.fadeOut();
			}
		});
		pagetop.click(function() {
			$('body, html').animate({ scrollTop: 0 }, 500);
			return false;
		});
	});	
	

	var cookie_name = 'favorite';
	var cookie_thumb = 'thumbnail';
	var favorite_items = [];
	var favorite_thumb = [];
	$('#fav-list').hide();
	function favorite(videoId, thumb){
		//お気に入りにvideoIdをset
		$('.favorite').val(videoId);
		$('.favorite').attr('img',thumb);
		favoriteJudge(videoId);
	}
	
	function favoriteJudge(videoId){
		//クッキーの存在確認
		if($.cookie(cookie_name)){
			favorite_items = $.cookie(cookie_name).split(",");
			favorite_thumb = $.cookie(cookie_thumb).split(",");
		}
		
		if($.inArray(videoId, favorite_items) < 0){
			$(".favorite").removeAttr('id');
			$(".favorite").attr('id', 'favorite-add');
			$(".favorite").children('span').text('お気に入りに追加');
		}
		else{
			$(".favorite").removeAttr('id');
			$(".favorite").attr('id', 'favorite-delete');
			$(".favorite").children('span').text('お気に入りに追加しました');
		}
	}
	
	function favoriteAdd(videoId, thumb){
		// cookieに7日間残るデータをset;
		if($.cookie(cookie_name)){
			favorite_items = $.cookie(cookie_name).split(",");
			favorite_thumb = $.cookie(cookie_thumb).split(",");
		}
		favorite_items.push(videoId);
		favorite_thumb.push(thumb);
		console.log(favorite_items);
		$.cookie(cookie_name, favorite_items, {expires : 7, path:'/'});
		$.cookie(cookie_thumb, favorite_thumb, {expires : 7, path:'/'});
		favoriteJudge(videoId);
	}
	
	function favoriteDelete(videoId, thumb){
		// cookieからfav削除
		favorite_items = $.cookie(cookie_name).split(",");
		favorite_thumb = $.cookie(cookie_thumb).split(",");
		for(i=0; i<favorite_items.length; i++){
			if(favorite_items[i] == videoId){
				favorite_items.splice(i, 1);
				favorite_thumb.splice(i, 1);
				break;
			}
		}
		$.cookie(cookie_name, favorite_items, {expires : 7, path:'/'});
		$.cookie(cookie_thumb, favorite_thumb, {expires : 7, path:'/'});
		console.log(favorite_items);
		favoriteJudge(videoId);
	}
	
	function favCnt() {
		//擬似static変数
		if ($.cookie('num') == null) {
			$.cookie('num', 0);
		}
		fav_cnt = $.cookie('num');
		$.cookie('num', $.cookie('num')+1, { expires: 7 });
		return 'fav'+fav_cnt;
	}
	
	function favList() {
		var result="";
		
		if($.cookie(cookie_name)){
			favorite_items = $.cookie(cookie_name).split(",");
			favorite_thumb = $.cookie(cookie_thumb).split(",");
			for(i=0; i<favorite_items.length; i++){
				result += "<img src='"+favorite_thumb[i]+"' class='img fav-img pointer' data-yt-id='"+favorite_items[i]+"' align='left'/><a class='fav-del-icon pointer'　img='"+favorite_thumb[i]+"' data-yt-id='"+favorite_items[i]+"'>×</a><br clear='left'>";
			}
		}
		else{
			result += 'お気に入りに<br>追加してください';
		}
		console.log(result);
		$("#fav-list").html(result);
	}
	
	$(".favorite").click(function(){
		var add_or_del;
		add_or_del = ($(this).attr('id') ==  'favorite-add')? true:false;
		console.log($(this).attr('img'));
		
		if(add_or_del)
			favoriteAdd($(this).val(),$(this).attr('img'));
		else
			favoriteDelete($(this).val(),$(this).attr('img'));
			
		favList();
	});
	
	$(document).on('click', '.fav-del-icon', (function(){
		favoriteDelete($(this).attr("data-yt-id"), $(this).attr("img"));
		favList();
	}));
	
	$("#fav-list-button").click(function(){
		$('#fav-list').toggle(200);
		$("#fav-list").mCustomScrollbar();
	});
	
});
function validate(){
	if($("#textbox").val().trim()=="") return false;
}

