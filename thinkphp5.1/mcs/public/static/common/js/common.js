$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function(index,val) {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [ o[this.name] ];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    //解决checkbox未选中时，没有序列化到对象中的代码
    var checkboxes = $(this).find('input[type=checkbox]');
    $.each(checkboxes, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            if($(this).prop('checked')) {
                o[this.name].push('1');
            }else{
                o[this.name].push('0');
            }
        }else{
            if($(this).prop('checked')){
                o[this.name] = '1';
            }else{
                o[this.name] = '0';
            }
        }
    });
    return o;
};