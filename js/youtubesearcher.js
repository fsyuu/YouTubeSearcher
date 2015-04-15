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
					loadPlayer(first.attr("data-yt-id"));
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
	function loadPlayer(videoID) {
		dbg("loadPlayer("+videoID+")");
		/* 埋め込むオブジェクトを生成（すでにある場合は削除）*/
		if(!player){
			player = new YT.Player(
				'player',{
					width: '820',   /* 動画プレーヤーの幅 */
					height: '500',   /* 動画プレーヤーの高さ */
					videoId: videoID,   /* YouTube動画ID */
					events: { /* イベント */
						"onReady": onPlayerReady   /* プレーヤの準備完了時 */
					}
				}
			);
		}else{
			player.loadVideoById(videoID); /* 指定した動画を読み込んで再生（自動再生扱い） */
		}
	}
	function onPlayerReady(event){
		dbg("onPlayerReady");
		event.target.setVolume(50);   /* 音量調整 */
		//event.target.playVideo();   /* 動画再生 */
	}
	/* 指定した動画を再生 */
	$(".img").click(function(){
		loadPlayer($(this).attr("data-yt-id"));
		$(".img").removeClass("border-trans");
		$(this).addClass("border-trans");
	});

	$(document).ready(function(){
		$("#search-form").validationEngine();
	});
	function dbg(str){
		if(window.console && window.console.log){
			console.log(str);
		}
	}
});

