webpackJsonp([32],{1:function(t,e){t.exports=function(){var t=[];return t.toString=function(){for(var t=[],e=0;e<this.length;e++){var r=this[e];r[2]?t.push("@media "+r[2]+"{"+r[1]+"}"):t.push(r[1])}return t.join("")},t.i=function(e,r){"string"==typeof e&&(e=[[null,e,""]]);for(var n={},i=0;i<this.length;i++){var a=this[i][0];"number"==typeof a&&(n[a]=!0)}for(i=0;i<e.length;i++){var o=e[i];"number"==typeof o[0]&&n[o[0]]||(r&&!o[2]?o[2]=r:r&&(o[2]="("+o[2]+") and ("+r+")"),t.push(o))}},t}},2:function(t,e,r){function n(t,e){for(var r=0;r<t.length;r++){var n=t[r],i=l[n.id];if(i){i.refs++;for(var a=0;a<i.parts.length;a++)i.parts[a](n.parts[a]);for(;a<n.parts.length;a++)i.parts.push(p(n.parts[a],e))}else{for(var o=[],a=0;a<n.parts.length;a++)o.push(p(n.parts[a],e));l[n.id]={id:n.id,refs:1,parts:o}}}}function i(t){for(var e=[],r={},n=0;n<t.length;n++){var i=t[n],a=i[0],o=i[1],s=i[2],p=i[3],c={css:o,media:s,sourceMap:p};r[a]?r[a].parts.push(c):e.push(r[a]={id:a,parts:[c]})}return e}function a(t,e){var r=h(),n=b[b.length-1];if("top"===t.insertAt)n?n.nextSibling?r.insertBefore(e,n.nextSibling):r.appendChild(e):r.insertBefore(e,r.firstChild),b.push(e);else{if("bottom"!==t.insertAt)throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");r.appendChild(e)}}function o(t){t.parentNode.removeChild(t);var e=b.indexOf(t);e>=0&&b.splice(e,1)}function s(t){var e=document.createElement("style");return e.type="text/css",a(t,e),e}function p(t,e){var r,n,i;if(e.singleton){var a=v++;r=g||(g=s(e)),n=c.bind(null,r,a,!1),i=c.bind(null,r,a,!0)}else r=s(e),n=d.bind(null,r),i=function(){o(r)};return n(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;n(t=e)}else i()}}function c(t,e,r,n){var i=r?"":n.css;if(t.styleSheet)t.styleSheet.cssText=x(e,i);else{var a=document.createTextNode(i),o=t.childNodes;o[e]&&t.removeChild(o[e]),o.length?t.insertBefore(a,o[e]):t.appendChild(a)}}function d(t,e){var r=e.css,n=e.media,i=e.sourceMap;if(n&&t.setAttribute("media",n),i&&(r+="\n/*# sourceURL="+i.sources[0]+" */",r+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(i))))+" */"),t.styleSheet)t.styleSheet.cssText=r;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(r))}}var l={},u=function(t){var e;return function(){return"undefined"==typeof e&&(e=t.apply(this,arguments)),e}},f=u(function(){return/msie [6-9]\b/.test(window.navigator.userAgent.toLowerCase())}),h=u(function(){return document.head||document.getElementsByTagName("head")[0]}),g=null,v=0,b=[];t.exports=function(t,e){e=e||{},"undefined"==typeof e.singleton&&(e.singleton=f()),"undefined"==typeof e.insertAt&&(e.insertAt="bottom");var r=i(t);return n(r,e),function(t){for(var a=[],o=0;o<r.length;o++){var s=r[o],p=l[s.id];p.refs--,a.push(p)}if(t){var c=i(t);n(c,e)}for(var o=0;o<a.length;o++){var p=a[o];if(0===p.refs){for(var d=0;d<p.parts.length;d++)p.parts[d]();delete l[p.id]}}}};var x=function(){var t=[];return function(e,r){return t[e]=r,t.filter(Boolean).join("\n")}}()},69:function(t,e,r){e=t.exports=r(1)(),e.push([t.id,".introduce{width:890px;margin:30px auto}.introduce .orange-icon{height:14px;width:4px;background:#ff791f;margin-top:4px;margin-right:10px;vertical-align:-2px}.introduce .introduce-tit{font-weight:700;font-size:14px}.introduce .introduce-bd{color:#666;margin-top:20px}.introduce .dif-matter{margin-left:24px}.introduce .spec-matter{margin-left:16px}.table-wrap{width:840px;margin:18px auto 30px}.table-wrap table{font-size:14px;border:1px solid #ccc}.table-wrap table th{font-weight:700;color:#666;background:#f5f5f5;height:36px;text-align:center}.table-wrap table td{color:gray;height:36px;text-align:center}.table-wrap table .grade{width:260px}.table-wrap table .section{width:296px}.table-wrap table .frequency{width:280px}",""])},124:function(t,e,r){var n=r(69);"string"==typeof n&&(n=[[t.id,n,""]]);r(2)(n,{});n.locals&&(t.exports=n.locals)},165:function(t,e){t.exports=' <div class="introduce clearfix"> <p class="orange-icon G_fl"></p> <p class=introduce-tit>什么是积分</p> <p class=introduce-bd>积分是购车惠为4S店用户提供的虚拟币，积分可兑换礼品。</p> </div> <div class="introduce clearfix"> <p class="orange-icon G_fl"></p> <p class=introduce-tit>积分等级及兑换次数标准</p> <div class=table-wrap> <table border=1 cellspacing=0 cellpadding=0> <tr> <th class=grade>等级</th> <th class=section>积分区间</th> <th class=frequency>单月有效兑换次数</th> </tr> <tr> <td>初级</td> <td>5000以下</td> <td>1</td> </tr> <tr> <td>中级</td> <td>5000-10000</td> <td>2</td> </tr> <tr> <td>高级</td> <td>10000以上</td> <td>3</td> </tr> </table> </div> </div> <div class="introduce clearfix"> <p class="orange-icon G_fl"></p> <p class=introduce-tit>积分获取规则</p> <p class=introduce-bd> 1）每个月有10个不同的车款的报价被判定为底价且需要超过24小时不上调价格，获得积分500，30款奖励800分</p> <p class=introduce-bd>2）以季度为单位（1-3月，4-6月，7-9月，10-12月），车款报价成为底价次数少于200次，扣除积分500</p> <p class=spec-matter>次数的计算方式</p> <p class=dif-matter>a、车款报价成为底价超过24小时算一次数</p> <p class=dif-matter>b、车款报价成为底价被计分，价格不变仍为底价的条件下后每3天为一个单位，计算一次</p> <p class=introduce-bd>3）底价车款每被询价一次，增加20积分。（不退单）</p> <p class=introduce-bd>4）如果因线上报价高于线下销售价格，发生询价退单，扣50积分</p> <p class=introduce-bd>5）产生定车订单（包括底价车和特价车），根据价格区间不同，获得不同的积分，价格与积分对应关系</p> <p class=dif-matter>a、15万以下得300积分</p> <p class=dif-matter>b、15-30万得500积分</p> <p class=dif-matter>c、30万以上得800积分</p> <p class=introduce-bd>6）如果有订车订单，无法完成轿车，扣除500积分</p> <p class=introduce-bd>7）月获得积分超过3000分的，月底再返还当月积分的30%</p> </div> '},268:function(t,e,r){var n,i,a={};r(124),i=r(165),t.exports=n||{},t.exports.__esModule&&(t.exports=t.exports["default"]);var o="function"==typeof t.exports?t.exports.options||(t.exports.options={}):t.exports;i&&(o.template=i),o.computed||(o.computed={}),Object.keys(a).forEach(function(t){var e=a[t];o.computed[t]=function(){return e}})}});