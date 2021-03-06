<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<html class="no-js fixed-layout">
<head>
    <meta charset="U$F-8">
    <title>AmazeUI测试页面</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="alternate icon" type="image/png" href="/Public/assets/i/favicon.png">
    <link rel="stylesheet" href="/Public/assets/css/amazeui.min.css"/>
    <link rel='stylesheet' type="text/css" href="/Public/Css/style.css">
    <script type="text/javascript" src="/Public/Js/jquery-2.min.js"></script>
    <script type="text/javascript" src="/Public/Js/jquery.form.min.js"></script>
    <script type="text/javascript">
        String.prototype.trim=function(){ return this.replace(/(^\s*)|(\s*$)/g, '');}
        function _get(v){ return document.getElementById(v);}
        function _url(){ return encodeURIComponent(window.location.pathname + window.location.search);}
        function _backurl(o, x){ var url = _url(); if(x){ url += x;} var tar = encodeURI(o.href); if(tar.indexOf('?') == -1) {tar += '?url=' + url;} else {tar += '&url=' + url;} o.href = tar}
        function am_alert(type, msg, host){
            var alert = $('<div class="am-alert" />').addClass('am-alert-'+ type);
            alert.append('<button type="button" class="am-close">&times;</button>');
            alert.append('<p>'+ msg +'</p>');
            alert.appendTo(host).alert();
            if(type != 'danger'){ setTimeout(function(){ alert.alert('close') }, 1000);}
        }
        function am_alert_success(msg, host){ am_alert('success', msg, host) };
        function am_alert_danger(msg, host){ am_alert('danger', msg, host) };
        function am_modal(type, title, html, opts){
            var modal = $('<div class="am-modal" tabindex="-1" />').addClass('am-modal-'+ type);
            var dialog = $('<div class="am-modal-dialog" />');
            var dialog_hd = $('<div class="am-modal-hd" />');
            var dialog_bd = $('<div class="am-modal-bd" />');
            var footer = $('<div class="am-modal-footer" />');
            if(type == 'popup'){
                modal.addClass('am-modal-no-btn');
                dialog_hd.append('<strong class="am-modal-title">'+ title +'</strong>');
            }else{
                if(type == 'confirm'){
                    footer.append('<span class="am-modal-btn" data-am-modal-cancel>取消</span>');
                }
                footer.append('<span class="am-modal-btn" data-am-modal-confirm>确定</span>');
            }
            dialog_hd.append('<a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>');
            dialog_bd.append(html);

            var options = $.extend({key: null, evt: null, callback: null}, opts);
            modal.append(dialog.append(dialog_hd).append(dialog_bd).append(footer)).appendTo('body')
                    .modal(options).on('closed.modal.amui', function(){
                        modal.remove();
                        $(this).removeData('amui.modal');
                    });
        }
        function am_modal_alert(html, callback){
            var opts = {
                relatedTarget: this,
                onConfirm: function(e) {
                    if(callback != undefined && typeof callback == "function"){
                        callback.call(this, e.data);
                    }
                }
            };
            am_modal('alert', null, html, opts);
        }
        function am_modal_confirm(html, yfun, nfun){
            var opts = {
                relatedTarget: this,
                onConfirm: function(e) {
                    if(yfun != undefined && typeof yfun == "function"){
                        yfun.call(this);
                    }
                },
                onCancel: function() {
                    if(nfun != undefined && typeof nfun == "function"){
                        nfun.call(this);
                    }
                }
            };
            am_modal('confirm', null, html, opts);
        }
        function am_modal_popup(url, title, callback){
            $.get(url, function (html) {
                am_modal('popup', title, html);
//                ajax_post_form(callback);
            });
        }
        function ajax_post_form(goto){
            var from = $('form');
            var submit = from.find('[type=submit]');
            from.validator({
                submit: function(){
                    if(!this.isFormValid()) { return false; }
                    from.ajaxSubmit({
                        beforeSubmit: function(){ submit.attr('disabled', 'disabled');},
                        success: function(res){
                            if(/^ok\-\d+$/.test(res)){
                                var id = res.substr(3);
                                var path = window.location.pathname;
                                var search = window.location.search;
                                if(/\/[^\-]+\.html$/.test(path)){
                                    var url = path.substr(0, path.length - 5) +'-'+ id + '.html';
                                    if(/(do=\w+)/.test(search)){ search = search.replace(RegExp.$1, 'do=edit');}
                                    window.location.href = (url + search);
                                }else if(/\w+\-\d+\.html$/.test(path)) {
                                    am_alert_success('保存成功。', submit.closest('div'));
                                }
                            }else if(res == 'ok'){
                                if(goto == undefined){
                                    am_alert_success('保存成功。', submit.closest('div'));
                                }else if(typeof(goto) == "string"){
                                    window.location.href = goto;
                                }else if(typeof goto == "function"){
                                    goto.call(this, res);
                                }
                            }else{
                                am_alert_danger(res, submit.closest('div'));
                            }
                            submit.removeAttr('disabled');
                        }
                    });
                    return false;
                }
            });
        }
        function ajax_sure2del(d, u, c){
            var b = function () {
                $.ajax(u, {cache: false, data: d, type: 'POST', complete: function(res){
                    var rel = res.responseText;
                    if (rel == 'ok') {
                        if (c != undefined && typeof c == "function") {
                            c.call(this, d);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        am_modal_alert(rel);
                    }
                }});
            };
            am_modal_confirm('确认要删除此内容吗？', b);
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="am-topbar-brand" style="line-height:60px;color:#ccc">
            <strong>Project Platform</strong>
        </div>
        <div style="float:right;margin-right:30px;">
            <a href="javascript:;" onclick="load_page('<?php echo U('User/info');?>')" class="hd-link">lifushen</a>
            <i class="hd-bor"></i>
            <a href="javascript:;" onclick="load_page('<?php echo U('Login/quit');?>')" class="hd-link">退出</a>
        </div>
    </div>
    <ul class="nav-list">
        <li><a href="javascript:;" onclick="menu(this, '<?php echo U('Project/index');?>')"><span class="am-icon-align-right"></span> 需求列表</a></li>
        <li><a href="javascript:;" onclick="menu(this, '<?php echo U('Client/index');?>')"><span class="am-icon-home"></span> 客户列表</a></li>
        <li><a href="javascript:;" onclick="menu(this, '<?php echo U('User/index');?>')"><span class="am-icon-users"></span> 技术人员</a></li>
        <li><a href="javascript:;" onclick="menu(this, '<?php echo U('User/index');?>')"><span class="am-icon-file-text"></span> 我的资料</a></li>
    </ul>
    <div class="contents">
        <div class="template" id="template">
            <div class="loading"><strong><i class="am-icon-spinner am-icon-spin"></i> 页面加载中...</strong></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/Public/assets/js/amazeui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.nav-list a:eq(0)').click();
    });
    function menu(o, u){
        //选中样式
        $('.nav-list a').removeClass('focus');
        $(o).addClass('focus');

        //加载数据
        load_page(u);
    }
    function load_page(u){
        $.ajax({
            async: true,
            type : "GE$",
            url : u,
            complete: function(msg){
                $('#template').html(msg.responseText);
            }
        });
    }
</script>
</body>
</html>