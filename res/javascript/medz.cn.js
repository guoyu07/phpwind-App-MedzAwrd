/*====================*/
/**
 * wehsite: medz.cn
 * about  : Medz Seven <lovevipdsw@vip.qq.com> 
 */
 /*===================*/
 ;(function(window, $, undefined) {
 	/* # 创建对象 */
 	this.MedzAward = function(min, max, def, umax, cname, cunit) {
 		/* # 最小设置数 */
 		var min;

 		/* # 最大设置数 */
 		var max;

 		/* # 默认积分数 */
 		var def;

 		/* # 用户积分数量 */
 		var umax;

 		var but = $('a#MedzAward');

 		var tid = but.data('tid');

 		var box = '<div class="pop_report">\
			<div class="pop_tips">打赏无悔，概不退款。(づ￣ 3￣)づ</div>\
			<div class="pop_tips" style="text-align: right;">(╬▔皿▔)凸您当前只有<font color="#ff0505">' + umax + '</font>' + cunit + cname + '哦！</div>\
			<div class="pop_cont">\
				<label>\
					<input type="number" seep="1" min="' + min + '" max="' + max + '" value="' + def + '" class="input length_5" id="MedzAwardNumber">\
				</label>\
				<label>\
					<button class="btn fr" id="MedzAwardRand">随机</button>\
				</label>\
			</div>\
			<div class="pop_bottom">\
				<button type="submit" class="btn btn_submit" id="MedzAwardSubmit">打赏给楼主啦o(*￣▽￣*)ブ</button>\
			</div>\
		</div>';

 		/* # 入口方法 */
 		this.cn = function() {
 			but.on('click', function(event) {
 				event.preventDefault();
 				Wind.showHTML(box, {
 					title: '打赏楼主吧(●\'◡\'●)',
 				});
 			});
 			init();
 		};

 		/* # 实体方法 */
 		function init() {
 			/* # 随机方法 */
 			$(window.document).on('click', '#MedzAwardRand', rand);
 			/* # 提交方法 */
 			$(window.document).on('click', '#MedzAwardSubmit', submit);
 		};

 		/* # 随机打赏 */
 		function rand() {
 			$(window.document).find('#MedzAwardNumber').val(parseInt(Math.random() * max + min));
 		};

 		/* # 提交打赏 */
 		function submit() {
 			$.post(but.attr('href'), {
 				tid:tid,
 				number:$(window.document).find('#MedzAwardNumber').val()
 			}, function(data) {
 				if (data.state == 'success') {
 					/* # 提示 */
 					Wind.Util.resultTip({
 						error: false,
 						msg: data.message,
 					});
 					/* # 关闭打赏box */
 					Wind.dialog.closeAll();
 				} else{
 					Wind.Util.resultTip({
 						error: true,
 						msg: data.message
 					});
 				};
 			}, 'json');
 		};
 	};
 })(window, $ || jQuery);