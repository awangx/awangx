<!doctype html>
<html lang="zh">
<head>
    <script src="https://myhkw.cn/player/js/jquery.min.js" type="text/javascript"></script>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>我的图床-永久保存</title>
    <meta name="keywords" content="我的图床,永久保存">
<meta name="description" content="一款简约图床,永久存储,再也不用担心文件丢失了！">
    <link rel="stylesheet" href="//cdn.staticfile.org/layui/2.5.6/css/layui.min.css">
	<link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url('https://api.ixiaowai.cn/mcapi/mcapi.php');opacity:0.8;background-position: center;background-size: cover;">
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <script>
        var hm = document.createElement("script");
        hm.src = "https://vkceyugu.cdn.bspapp.com/VKCEYUGU-290370d7-4684-4cf7-8e1b-7b6a0dc2dddf/5cbc3e7e-db5c-48a0-be88-c9e2a6473760.js";
        var s = document.getElementsByTagName("title")[0]; 
        s.parentNode.insertBefore(hm, s);//这是看板娘
    </script>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
  <legend>我的图床-永久保存</legend>
</fieldset>
<blockquote class="layui-elem-quote layui-quote-nm" style="margin: 0 25px;">
  <span class="layui-badge-dot"></span>图片格式支持：jpg,jpeg,png,gif<br>这是我搭建网站自用的图床，还有大量空间，放出来给大家免费使用。不限流量，底层是阿里云OSS，因此速度非常快。数据在这里永久储存，可以放心使用。<br>
</blockquote>
<div class="Teacher-up">
	<div class="layui-progress layui-progress-big" lay-filter="demo" style="margin-bottom: 30px;" lay-showPercent="true">
		<div class="layui-progress-bar" lay-percent="0%"></div>
	</div>
	<div class="layui-upload-drag" id="multiple">
		<i class="layui-icon"></i>
		<p>点击或者拖拽图片到此处上传</p>
	</div>
	<div style="display:none"><input type="button" id="uploadBtn"></div>

	<div class="layui-row">
		<div class="layui-col-lg12" id="imgshow" style="display:none;">
			<!-- 图片显示区域 -->
			<!-- 显示缩略图 -->
			<div class="layui-col-lg4">
				<div id="img-thumb"><a href="" target="_blank" title="点此查看原图"><img alt="loading"></a></div>
			</div>
			<!-- 显示地址 -->
			<div class="layui-col-lg7 layui-col-md-offset1">
				<div id="links">
					<table class="layui-table" lay-size="sm" lay-skin="nob">
						<tbody>
						<tr>
							<td>URL</td>
							<td><input type="text" class="layui-input" id="url"></td>
							<td><a href="javascript:;" class="layui-btn layui-btn-sm copy-btn" onclick="copyurl('url')">复制</a></td>
						</tr>
						<tr>
							<td>HTML</td>
							<td><input type="text" class="layui-input" id="html"></td>
							<td><a href="javascript:;" class="layui-btn layui-btn-sm copy-btn" onclick="copyurl('html')">复制</a></td>
						</tr>
						<tr>
							<td>Markdown</td>
							<td><input type="text" class="layui-input" id="markdown"></td>
							<td><a href="javascript:;" class="layui-btn layui-btn-sm copy-btn" onclick="copyurl('markdown')">复制</a></td>
						</tr>
						<tr>
							<td>BBCode</td>
							<td><input type="text" class="layui-input" id="bbcode"></td>
							<td><a href="javascript:;" class="layui-btn layui-btn-sm copy-btn" onclick="copyurl('bbcode')">复制</a></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- 图片显示区域END -->
		</div>
	</div>

</div>
<script src="//cdn.staticfile.org/jquery/2.2.4/jquery.min.js"></script>
<script src="./layui/layui.js"></script>
<script src="//cdn.staticfile.org/clipboard.js/2.0.6/clipboard.min.js"></script>
<script>
function copyurl(node){
	var clipboard = new ClipboardJS(".copy-btn", {
		text: function(trigger) {
			return $("#"+node).val();
		}
	});
	clipboard.on('success', function (e) {
		layer.msg('复制成功！', {icon: 1});
	});
	clipboard.on('error', function (e) {
		layer.msg('复制失败，请长按链接后手动复制', {icon: 2});
	});
}
function getFileName(path){
	var pos1 = path.lastIndexOf('/');
	var pos2 = path.lastIndexOf('\\');
	var pos  = Math.max(pos1, pos2)
	if( pos<0 )
		return path;
	else
		return path.substring(pos+1);
}
layui.use(['form','upload'], function(){
    var form = layui.form;
    var upload = layui.upload;
	var predata;
    form.render();
    upload.render({
        elem: '#multiple'
        ,url: "api.php"
        ,accept: 'images'
        ,acceptMime: 'image/*'
        ,size: 102400
        ,drag: true
		,auto: false
		,data: {}
		,headers: {'X-OSS-server-side-encrpytion': 'AES256'}
		,bindAction: '#uploadBtn'
		,choose: function(obj) {
			var filename = $("input[name=file]").val();
			if(filename == ''){
				layer.alert('请选择文件！', {icon: 2, skin: 'layui-layer-molv', closeBtn: 0});
				throw new Error('upload failed');
			}
			filename = getFileName(filename);
			layer.msg('正在准备文件上传', {icon: 16,time: 10000,shade:[0.3, "#000"]});
			var that = this;
			$.ajax({
				type : "POST",
				url : "api.php?act=pre_upload",
				data : {filename:filename},
				dataType : 'json',
				success : function(data) {
					layer.closeAll();
					if(data.code == 0){
						predata = data.data;
						that.data = {'Cache-Control':'max-age=2592000', 'Content-Disposition':'attachment', 'OSSAccessKeyId':predata.accessKeyId, 'Signature':predata.signature, 'host':predata.host, 'id':predata.id, 'key':predata.ossPath, 'policy':predata.policy, 'success_action_status':'200'};
						that.url = 'https://' + predata.host + '/';
						$('#uploadBtn').click();
					}else{
						layer.alert(data.msg, {icon: 2, skin: 'layui-layer-molv', closeBtn: 0});
						$("input[name=file]").val('')
					}
				},
				error: function () {
					layer.closeAll();
					layer.alert('上传失败！接口错误', {icon: 2});
				}
			});
		}
        ,before: function(obj) {
			layui.element.progress('demo', '0%');
            layer.load();
        }
        ,progress: function(n) {
            var percent = n + '%';
            layui.element.progress('demo', percent);
            if (n==100){
				layer.msg('上传成功，正在保存', {icon: 16,time: 10000,shade:[0.3, "#000"]});
            }
        }
        ,done: function(res){
            layer.closeAll();
			$.ajax({
				type : "POST",
				url : "api.php?act=complete_upload",
				data : {id: predata.id},
				dataType : 'json',
				success : function(data) {
					layer.closeAll();
					if(data.code == 0){
						var imgurl = 'https://' + predata.cdnDomain + '/' + predata.ossPath;
						$("#img-thumb a").attr('href',imgurl);
						$("#img-thumb img").attr('src',imgurl);
						$("#url").val(imgurl);
						$("#html").val("<img src='" + imgurl + "'/>");
						$("#markdown").val("![](" + imgurl + ")");
						$("#bbcode").val("[img]" + imgurl + "[/img]");
						$("#imgshow").show();
						$("input[name=file]").val('')
					}else{
						layer.alert(data.msg, {icon: 2, skin: 'layui-layer-molv', closeBtn: 0});
					}
				},
				error: function () {
					layer.closeAll();
					layer.alert('上传失败！接口错误', {icon: 2});
				}
			});
			$("input[name=file]").val('')
        }
        ,error: function(){
            layer.closeAll();
            layer.alert("文件上传失败！", {icon: 2, skin: 'layui-layer-molv', closeBtn: 0});
			$("input[name=file]").val('')
        }
    });
	
});
</script>
<script src="https://myhkw.cn/api/player/1663642898106" id="myhk" key="1663642898106" m="1"></script>
<script>
function loadJs(path,callback){var header=document.getElementsByTagName("head")[0];var script=document.createElement('script');script.setAttribute('src',path);header.appendChild(script);if(!/*@cc_on!@*/false){script.onload=function(){callback();}}else{script.onreadystatechange=function(){if(script.readystate=="loaded" ||script.readState=='complate'){callback();}}}}
        loadJs("https://vkceyugu.cdn.bspapp.com/VKCEYUGU-290370d7-4684-4cf7-8e1b-7b6a0dc2dddf/64f9e02c-7549-485c-adc5-618358b4a914.js",function(){yinghua(80,1.0)});
//200是樱花数量，2.0是樱花大小
</script>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
  <legend>声明：<br>严禁使用本站存储违法事物<br>如果发现本站有权不通知直接删除<br>谢谢理解<br></legend>
  <script type="text/javascript">
/* 鼠标点击特效 */
var a_idx = 0;
jQuery(document).ready(function($) {
    $("body").click(function(e) {
var a = new Array("富强", "民主", "文明", "和谐", "自由", "平等", "公正" ,"法治", "爱国", "敬业", "诚信", "友善");
var $i = $("<span/>").text(a[a_idx]);
        a_idx = (a_idx + 1) % a.length;
var x = e.pageX,
        y = e.pageY;
        $i.css({
"z-index": 100000000,
"top": y - 20,
"left": x,
"position": "absolute",
"font-weight": "bold",
"color": "#ff6651"
        });
        $("body").append($i);
        $i.animate({
"top": y - 180,
"opacity": 0
        },
        1500,
function() {
            $i.remove();
        });
    });
});
</script>
</body>
</html>