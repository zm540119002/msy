<?php /*a:5:{s:81:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/custom_client/tpl.html";i:1551064593;s:93:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/custom_client/custom_list_tpl.html";i:1551064593;s:93:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/custom_client/custom_info_tpl.html";i:1551064593;s:94:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/custom_client/message_list_tpl.html";i:1551064593;s:94:"/home/www/web/thinkphp5.1/mch/application/index_admin/view/custom_client/message_info_tpl.html";i:1551064593;}*/ ?>
<div class="left chat_left">
	<div class="left_top">
		<div class="top_sign">
			<img src="http://mch.meishangyun.com/static/common/img/default/weiya_logo.png" alt="" class="left">
			<span>维雅客服</span>
		</div>
		<div class="top_search">
			<input type="button" class="left search_btn"/>
			<input type="text" class="left search_text" placeholder="搜索联系人" />
		</div>
	</div>
	<ul class="custom_list">
		<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;if(!(empty($info) || (($info instanceof \think\Collection || $info instanceof \think\Paginator ) && $info->isEmpty()))): ?>
<li class="custom_item" data-from_id="<?php echo htmlentities($info['from_id']); ?>">
	<a href="javascript:void(0);" class="delete_chat">删除</a>
	<div class="avatar">
		<?php if(empty($info['avatar']) || (($info['avatar'] instanceof \think\Collection || $info['avatar'] instanceof \think\Paginator ) && $info['avatar']->isEmpty())): ?>
		<img src="http://mch.meishangyun.com/static/common/img/default/chat_head.jpg">
		<?php else: ?>
		<img src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($info['avatar']); ?>">
		<?php endif; ?>
		<span class="news_num"><?php if($info['unreadCount']): ?><?php echo htmlentities($info['unreadCount']); endif; ?></span>
	</div>
	<div class="content">
		<p class="author"><?php echo htmlentities($info['name']); ?></p>
	</div>
</li>
<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
	</ul>
</div>
<div class="chat_right chat_main_wrapper">
	<div class="chatLayer chat_dialog_content">
		<div class="ws_chatMsg-panel flex1">
			<div class="chatMsg-ct">
				<?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;if(!(empty($info) || (($info instanceof \think\Collection || $info instanceof \think\Paginator ) && $info->isEmpty()))): ?>
<ul class="clearfix message_list" data-from_id="<?php echo htmlentities($info['from_id']); ?>">
	<?php if(is_array($info['messages']) || $info['messages'] instanceof \think\Collection || $info['messages'] instanceof \think\Paginator): $i = 0; $__LIST__ = $info['messages'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$message): $mod = ($i % 2 );++$i;?>
	<li><span class="create_time"><?php echo htmlentities(date('Y-m-d H:i',!is_numeric($message['create_time'])? strtotime($message['create_time']) : $message['create_time'])); ?></span></li>
	<li class="message_item <?php echo htmlentities($message['who']); if($message['read']): ?> read<?php else: ?> unread<?php endif; ?>" data-id="<?php echo htmlentities($message['id']); ?>">
		<div class="avatar">
			<?php if(empty($message['avatar']) || (($message['avatar'] instanceof \think\Collection || $message['avatar'] instanceof \think\Paginator ) && $message['avatar']->isEmpty())): ?>
			<img src="http://mch.meishangyun.com/static/common/img/default/chat_head.jpg">
			<?php else: ?>
			<img src="http://mch.meishangyun.com/uploads/<?php echo htmlentities($message['avatar']); ?>">
			<?php endif; ?>
		</div>
		<div class="content">
			<div class="msg"><?php echo htmlentities($message['content']); ?></div>
		</div>
	</li>
	<?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
			</div>
		</div>
	</div>
	<div class="bottom_nav_fixed">
		<div class="bottom_flex">
			<input class="send_out_text" type="text" name="" value="">
			<input class="send_btn" type="button" value="发送">
		</div>
	</div>
</div>