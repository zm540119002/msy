<?php /*a:1:{s:63:"D:\web\thinkphp5.1\mch\application/index/view\address\info.html";i:1551065534;}*/ ?>
<div class="item_addr">
    <div class="ia-l"></div>
    <div class="item_info">
        <input type="hidden" value="<?php echo htmlentities($data['id']); ?>" class="address_id">
        <div class="mt_new">
            <span><?php echo htmlentities($data['consignee']); ?></span>
            <span><?php echo htmlentities($data['mobile']); ?></span>
            <?php if($data['is_default'] == 1): ?>
            <i class="default_tipc">默认</i>
            <?php endif; ?>
        </div>
        <p class=""><span class="area_address" id="<?php echo htmlentities($data['id']); ?>"></span><?php echo htmlentities($data['detail_address']); ?></p>
        <form style="display:none;" class="consigneeInfo">
            <input type="hidden" class="" name="province" value="<?php echo htmlentities($data['province']); ?>" />
            <input type="hidden" class="" name="city" value="<?php echo htmlentities($data['city']); ?>" />
            <input type="hidden" class="" name="area" value="<?php echo htmlentities($data['area']); ?>" />
            <input type="hidden" class="" name="layer_id" value="<?php echo htmlentities($data['id']); ?>" />
            <input type="hidden" class="" name="layer_consignee" value="<?php echo htmlentities($data['consignee']); ?>" />
            <input type="hidden" class="" name="layer_mobile" value="<?php echo htmlentities($data['mobile']); ?>" />
            <input type="hidden" class="" name="layer_detail_address" value="<?php echo htmlentities($data['detail_address']); ?>" />
            <input type="hidden" class="" name="is_default" value="<?php echo htmlentities($data['is_default']); ?>" />
        </form>
    </div>
    <div class="edit_operate">
        <!--<a href="javascript:void(0)" class="address_delete">删除</a>-->
        <a href="javascript:void(0);" class="select_address">选择地址></a>
        <a href="javascript:void(0);" class="ia-r address_edit">
            <span class="iar-icon"></span>
        </a>
    </div>
</div>