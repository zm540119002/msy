/*
 Highcharts JS v3.0.6 (2013-10-04)
 Prototype adapter

 @author Michael Nelson, Torstein Hønsi.

 Feel free to use and modify this script.
 Highcharts license: www.highcharts.com/license.
*/
var HighchartsAdapter=function(){var f=typeof Effect!=="undefined";return{init:function(a){if(f)Effect.HighchartsTransition=Class.create(Effect.Base,{initialize:function(b,c,d,g){var e;this.element=b;this.key=c;e=b.attr?b.attr(c):$(b).getStyle(c);if(c==="d")this.paths=a.init(b,b.d,d),this.toD=d,e=0,d=1;this.start(Object.extend(g||{},{from:e,to:d,attribute:c}))},setup:function(){HighchartsAdapter._extend(this.element);if(!this.element._highchart_animation)this.element._highchart_animation={};this.element._highchart_animation[this.key]=
this},update:function(b){var c=this.paths,d=this.element;c&&(b=a.step(c[0],c[1],b,this.toD));d.attr?d.element&&d.attr(this.options.attribute,b):(c={},c[this.options.attribute]=b,$(d).setStyle(c))},finish:function(){this.element&&this.element._highchart_animation&&delete this.element._highchart_animation[this.key]}})},adapterRun:function(a,b){return parseInt($(a).getStyle(b),10)},getScript:function(a,b){var c=$$("head")[0];c&&c.appendChild((new Element("script",{type:"text/javascript",src:a})).observe("load",
b))},addNS:function(a){var b=/^(?:click|mouse(?:down|up|over|move|out))$/;return/^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/.test(a)||b.test(a)?a:"h:"+a},addEvent:function(a,b,c){a.addEventListener||a.attachEvent?Event.observe($(a),HighchartsAdapter.addNS(b),c):(HighchartsAdapter._extend(a),a._highcharts_observe(b,c))},animate:function(a,b,c){var d,c=c||{};c.delay=0;c.duration=(c.duration||500)/1E3;c.afterFinish=c.complete;if(f)for(d in b)new Effect.HighchartsTransition($(a),
d,b[d],c);else{if(a.attr)for(d in b)a.attr(d,b[d]);c.complete&&c.complete()}a.attr||$(a).setStyle(b)},stop:function(a){var b;if(a._highcharts_extended&&a._highchart_animation)for(b in a._highchart_animation)a._highchart_animation[b].cancel()},each:function(a,b){$A(a).each(b)},inArray:function(a,b,c){return b?b.indexOf(a,c):-1},offset:function(a){return $(a).cumulativeOffset()},fireEvent:function(a,b,c,d){a.fire?a.fire(HighchartsAdapter.addNS(b),c):a._highcharts_extended&&(c=c||{},a._highcharts_fire(b,
c));c&&c.defaultPrevented&&(d=null);d&&d(c)},removeEvent:function(a,b,c){$(a).stopObserving&&(b&&(b=HighchartsAdapter.addNS(b)),$(a).stopObserving(b,c));window===a?Event.stopObserving(a,b,c):(HighchartsAdapter._extend(a),a._highcharts_stop_observing(b,c))},washMouseEvent:function(a){return a},grep:function(a,b){return a.findAll(b)},map:function(a,b){return a.map(b)},_extend:function(a){a._highcharts_extended||Object.extend(a,{_highchart_events:{},_highchart_animation:null,_highcharts_extended:!0,
_highcharts_observe:function(b,a){this._highchart_events[b]=[this._highchart_events[b],a].compact().flatten()},_highcharts_stop_observing:function(b,a){b?a?this._highchart_events[b]=[this._highchart_events[b]].compact().flatten().without(a):delete this._highchart_events[b]:this._highchart_events={}},_highcharts_fire:function(a,c){var d=this;(this._highchart_events[a]||[]).each(function(a){if(!c.stopped)c.preventDefault=function(){c.defaultPrevented=!0},c.target=d,a.bind(this)(c)===!1&&c.preventDefault()}.bind(this))}})}}}();
