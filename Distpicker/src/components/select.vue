<template>
    <div class="bg-grey" @click.self="toParent" v-if="!show">
        <mt-picker :slots="slots" @change="onValuesChange" value-key="value" showToolbar>
            <mt-header title="选择省市区">
                <mt-button slot="left" class="cancle" @click="toParent">取消</mt-button> 
                <mt-button slot="right" class="sure" @click="sure">确定</mt-button>                       
            </mt-header>   
        </mt-picker> 
    </div>
</template>
<script>
import { iosProvinces ,iosCitys, iosCountys} from '../assets/js/data'
export default {
    data(){
        return{
            result: '',
            provinceValue: '',
            cityValue: '',
            countyValue: '',
            first: '', 
            second: '', 
            third: '',
            slots: [{
                    flex: 1,
                    values: iosProvinces,
                    className: 'slot1',
                    textAlign: 'left',
                    defaultIndex: 0
                }, {
                    flex: 1,
                    values: iosCitys,
                    className: 'slot2',
                    textAlign: 'center',
                    defaultIndex: 0                    
                }, {
                    flex: 1,
                    values: iosCountys,
                    className: 'slot3',
                    textAlign: 'right',
                    defaultIndex: 0                    
                }]
        }
    },
    props: ['show', 'province', 'city', 'county'],
    methods: {
        // 筛选器值变化时调用
        onValuesChange(picker, values) {
            var cityArray = [],
                countyArray = [];
            if(values[0]){          
                for(let i=0; i<iosCitys.length; i++){
                    if(values[0].id == iosCitys[i].parentId){
                        cityArray.push(iosCitys[i]);
                    }
                }
                picker.setSlotValues(1, cityArray); 
            } 

            if(values[1]){                         
                for(let j=0; j<iosCountys.length; j++){
                    if(values[1].id == iosCountys[j].parentId){
                        countyArray.push(iosCountys[j]);
                    }
                }
                picker.setSlotValues(2, countyArray);
            }
            var result = this.result;
            
            if(typeof(values[1]) == 'object' && typeof(values[2]) == 'object'){
                result = values[0].value + values[1].value + values[2].value;
                this.provinceValue = values[0].value;
                this.cityValue = values[1].value;
                this.countyValue = values[2].value;
                for(let i=0; i<iosProvinces.length; i++){
                    if(iosProvinces[i].value == this.province){
                        this.first = i;
                    }
                }
                for(let i=0; i<cityArray.length; i++){
                    if(this.city == cityArray[i].value){
                        this.second = i;
                    }
                }
                for(var j=0; j<countyArray.length; j++){
                    if(this.county == countyArray[j].value){
                        this.third = j;
                    }
                }
                this.slots[0].defaultIndex = parseInt(this.first);
                this.slots[1].defaultIndex = parseInt(this.second);
            }else if(typeof(values[1]) != 'object' && typeof(values[2]) == 'object'){
                values[1] = this.iosCitys;
                result = values[0].value + this.city + values[2].value;
                this.provinceValue = values[0].value;
                this.cityValue = this.city;
                this.countyValue = values[2].value;
            }
            this.result = result;
        },
        // 传值
        toParent(){
            var show = false;
            var result, province, city, county;
            if(this.province + this.city + this.county){
                result = this.province + this.city + this.county;
                province = this.province;
                city = this.city;
                county = this.county;
            }
            this.$emit('changeValue', {show, result, province, city, county});
        },
        // 点击确定按钮
        sure(){
            var show = false;
            var result = this.result;
            var province = this.provinceValue;
            var city = this.cityValue;
            var county = this.countyValue;
            this.$emit('changeValue', {show, result, province, city, county});
        }
    },
    mounted(){
        this.slots[1].values = [];
        this.slots[2].values = [];
    },
    updated: function () {
        this.$nextTick(function () {
            console.log(this.province + this.city + this.county);
            this.slots[2].defaultIndex = parseInt(this.third);    
        })
    }
}
</script>
<style scoped>
    .bg-grey{
        background: rgba(0,0,0,.5);
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 10;
        top: 0;
    }
    .picker{
        position: fixed;
        bottom: 0;
        background: #fff;
        width: 100%;
    }
    .mint-header{
        background: #f7f7f8;
        color: #333;
    }
    .cancle{
        color: #999;
    }
    .sure{
        color: #0575f2;
    }
</style>


