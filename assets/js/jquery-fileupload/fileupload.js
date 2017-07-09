/*!
 * jQuery UI Widget 1.10.4+amd
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/jQuery.widget/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a,b){var c=0,d=Array.prototype.slice,e=a.cleanData;a.cleanData=function(b){for(var d,c=0;null!=(d=b[c]);c++)try{a(d).triggerHandler("remove")}catch(f){}e(b)},a.widget=function(b,c,d){var e,f,g,h,i={},j=b.split(".")[0];b=b.split(".")[1],e=j+"-"+b,d||(d=c,c=a.Widget),a.expr[":"][e.toLowerCase()]=function(b){return!!a.data(b,e)},a[j]=a[j]||{},f=a[j][b],g=a[j][b]=function(a,b){return this._createWidget?(arguments.length&&this._createWidget(a,b),void 0):new g(a,b)},a.extend(g,f,{version:d.version,_proto:a.extend({},d),_childConstructors:[]}),h=new c,h.options=a.widget.extend({},h.options),a.each(d,function(b,d){return a.isFunction(d)?(i[b]=function(){var a=function(){return c.prototype[b].apply(this,arguments)},e=function(a){return c.prototype[b].apply(this,a)};return function(){var f,b=this._super,c=this._superApply;return this._super=a,this._superApply=e,f=d.apply(this,arguments),this._super=b,this._superApply=c,f}}(),void 0):(i[b]=d,void 0)}),g.prototype=a.widget.extend(h,{widgetEventPrefix:f?h.widgetEventPrefix||b:b},i,{constructor:g,namespace:j,widgetName:b,widgetFullName:e}),f?(a.each(f._childConstructors,function(b,c){var d=c.prototype;a.widget(d.namespace+"."+d.widgetName,g,c._proto)}),delete f._childConstructors):c._childConstructors.push(g),a.widget.bridge(b,g)},a.widget.extend=function(c){for(var h,i,e=d.call(arguments,1),f=0,g=e.length;g>f;f++)for(h in e[f])i=e[f][h],e[f].hasOwnProperty(h)&&i!==b&&(c[h]=a.isPlainObject(i)?a.isPlainObject(c[h])?a.widget.extend({},c[h],i):a.widget.extend({},i):i);return c},a.widget.bridge=function(c,e){var f=e.prototype.widgetFullName||c;a.fn[c]=function(g){var h="string"==typeof g,i=d.call(arguments,1),j=this;return g=!h&&i.length?a.widget.extend.apply(null,[g].concat(i)):g,h?this.each(function(){var d,e=a.data(this,f);return e?a.isFunction(e[g])&&"_"!==g.charAt(0)?(d=e[g].apply(e,i),d!==e&&d!==b?(j=d&&d.jquery?j.pushStack(d.get()):d,!1):void 0):a.error("no such method '"+g+"' for "+c+" widget instance"):a.error("cannot call methods on "+c+" prior to initialization; "+"attempted to call method '"+g+"'")}):this.each(function(){var b=a.data(this,f);b?b.option(g||{})._init():a.data(this,f,new e(g,this))}),j}},a.Widget=function(){},a.Widget._childConstructors=[],a.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(b,d){d=a(d||this.defaultElement||this)[0],this.element=a(d),this.uuid=c++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=a.widget.extend({},this.options,this._getCreateOptions(),b),this.bindings=a(),this.hoverable=a(),this.focusable=a(),d!==this&&(a.data(d,this.widgetFullName,this),this._on(!0,this.element,{remove:function(a){a.target===d&&this.destroy()}}),this.document=a(d.style?d.ownerDocument:d.document||d),this.window=a(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:a.noop,_getCreateEventData:a.noop,_create:a.noop,_init:a.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(a.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:a.noop,widget:function(){return this.element},option:function(c,d){var f,g,h,e=c;if(0===arguments.length)return a.widget.extend({},this.options);if("string"==typeof c)if(e={},f=c.split("."),c=f.shift(),f.length){for(g=e[c]=a.widget.extend({},this.options[c]),h=0;h<f.length-1;h++)g[f[h]]=g[f[h]]||{},g=g[f[h]];if(c=f.pop(),1===arguments.length)return g[c]===b?null:g[c];g[c]=d}else{if(1===arguments.length)return this.options[c]===b?null:this.options[c];e[c]=d}return this._setOptions(e),this},_setOptions:function(a){var b;for(b in a)this._setOption(b,a[b]);return this},_setOption:function(a,b){return this.options[a]=b,"disabled"===a&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!b).attr("aria-disabled",b),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(b,c,d){var e,f=this;"boolean"!=typeof b&&(d=c,c=b,b=!1),d?(c=e=a(c),this.bindings=this.bindings.add(c)):(d=c,c=this.element,e=this.widget()),a.each(d,function(d,g){function h(){return b||f.options.disabled!==!0&&!a(this).hasClass("ui-state-disabled")?("string"==typeof g?f[g]:g).apply(f,arguments):void 0}"string"!=typeof g&&(h.guid=g.guid=g.guid||h.guid||a.guid++);var i=d.match(/^(\w+)\s*(.*)$/),j=i[1]+f.eventNamespace,k=i[2];k?e.delegate(k,j,h):c.bind(j,h)})},_off:function(a,b){b=(b||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,a.unbind(b).undelegate(b)},_delay:function(a,b){function c(){return("string"==typeof a?d[a]:a).apply(d,arguments)}var d=this;return setTimeout(c,b||0)},_hoverable:function(b){this.hoverable=this.hoverable.add(b),this._on(b,{mouseenter:function(b){a(b.currentTarget).addClass("ui-state-hover")},mouseleave:function(b){a(b.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(b){this.focusable=this.focusable.add(b),this._on(b,{focusin:function(b){a(b.currentTarget).addClass("ui-state-focus")},focusout:function(b){a(b.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(b,c,d){var e,f,g=this.options[b];if(d=d||{},c=a.Event(c),c.type=(b===this.widgetEventPrefix?b:this.widgetEventPrefix+b).toLowerCase(),c.target=this.element[0],f=c.originalEvent)for(e in f)e in c||(c[e]=f[e]);return this.element.trigger(c,d),!(a.isFunction(g)&&g.apply(this.element[0],[c].concat(d))===!1||c.isDefaultPrevented())}},a.each({show:"fadeIn",hide:"fadeOut"},function(b,c){a.Widget.prototype["_"+b]=function(d,e,f){"string"==typeof e&&(e={effect:e});var g,h=e?e===!0||"number"==typeof e?c:e.effect||c:b;e=e||{},"number"==typeof e&&(e={duration:e}),g=!a.isEmptyObject(e),e.complete=f,e.delay&&d.delay(e.delay),g&&a.effects&&a.effects.effect[h]?d[b](e):h!==b&&d[h]?d[h](e.duration,e.easing,f):d.queue(function(c){a(this)[b](),f&&f.call(d[0]),c()})}})});

/*
 * jQuery Iframe Transport Plugin 1.8.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2011, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):a(window.jQuery)}(function(a){"use strict";var b=0;a.ajaxTransport("iframe",function(c){if(c.async){var e,f,g,d=c.initialIframeSrc||"javascript:false;";return{send:function(h,i){e=a('<form style="display:none;"></form>'),e.attr("accept-charset",c.formAcceptCharset),g=/\?/.test(c.url)?"&":"?","DELETE"===c.type?(c.url=c.url+g+"_method=DELETE",c.type="POST"):"PUT"===c.type?(c.url=c.url+g+"_method=PUT",c.type="POST"):"PATCH"===c.type&&(c.url=c.url+g+"_method=PATCH",c.type="POST"),b+=1,f=a('<iframe src="'+d+'" name="iframe-transport-'+b+'"></iframe>').bind("load",function(){var b,g=a.isArray(c.paramName)?c.paramName:[c.paramName];f.unbind("load").bind("load",function(){var b;try{if(b=f.contents(),!b.length||!b[0].firstChild)throw new Error}catch(c){b=void 0}i(200,"success",{iframe:b}),a('<iframe src="'+d+'"></iframe>').appendTo(e),window.setTimeout(function(){e.remove()},0)}),e.prop("target",f.prop("name")).prop("action",c.url).prop("method",c.type),c.formData&&a.each(c.formData,function(b,c){a('<input type="hidden"/>').prop("name",c.name).val(c.value).appendTo(e)}),c.fileInput&&c.fileInput.length&&"POST"===c.type&&(b=c.fileInput.clone(),c.fileInput.after(function(a){return b[a]}),c.paramName&&c.fileInput.each(function(b){a(this).prop("name",g[b]||c.paramName)}),e.append(c.fileInput).prop("enctype","multipart/form-data").prop("encoding","multipart/form-data"),c.fileInput.removeAttr("form")),e.submit(),b&&b.length&&c.fileInput.each(function(c,d){var e=a(b[c]);a(d).prop("name",e.prop("name")).attr("form",e.attr("form")),e.replaceWith(d)})}),e.append(f).appendTo(document.body)},abort:function(){f&&f.unbind("load").prop("src",d),e&&e.remove()}}}}),a.ajaxSetup({converters:{"iframe text":function(b){return b&&a(b[0].body).text()},"iframe json":function(b){return b&&a.parseJSON(a(b[0].body).text())},"iframe html":function(b){return b&&a(b[0].body).html()},"iframe xml":function(b){var c=b&&b[0];return c&&a.isXMLDoc(c)?c:a.parseXML(c.XMLDocument&&c.XMLDocument.xml||a(c.body).html())},"iframe script":function(b){return b&&a.globalEval(a(b[0].body).text())}}})});

/*
 * jQuery File Upload Plugin 5.40.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery","jquery.ui.widget"],a):a(window.jQuery)}(function(a){"use strict";a.support.fileInput=!(new RegExp("(Android (1\\.[0156]|2\\.[01]))|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1\\.0|2\\.[05]|3\\.0))").test(window.navigator.userAgent)||a('<input type="file">').prop("disabled")),a.support.xhrFileUpload=!(!window.ProgressEvent||!window.FileReader),a.support.xhrFormDataFileUpload=!!window.FormData,a.support.blobSlice=window.Blob&&(Blob.prototype.slice||Blob.prototype.webkitSlice||Blob.prototype.mozSlice),a.widget("blueimp.fileupload",{options:{dropZone:a(document),pasteZone:a(document),fileInput:void 0,replaceFileInput:!0,paramName:void 0,singleFileUploads:!0,limitMultiFileUploads:void 0,limitMultiFileUploadSize:void 0,limitMultiFileUploadSizeOverhead:512,sequentialUploads:!1,limitConcurrentUploads:void 0,forceIframeTransport:!1,redirect:void 0,redirectParamName:void 0,postMessage:void 0,multipart:!0,maxChunkSize:void 0,uploadedBytes:void 0,recalculateProgress:!0,progressInterval:100,bitrateInterval:500,autoUpload:!0,messages:{uploadedBytes:"Uploaded bytes exceed file size"},i18n:function(b,c){return b=this.messages[b]||b.toString(),c&&a.each(c,function(a,c){b=b.replace("{"+a+"}",c)}),b},formData:function(a){return a.serializeArray()},add:function(b,c){return b.isDefaultPrevented()?!1:((c.autoUpload||c.autoUpload!==!1&&a(this).fileupload("option","autoUpload"))&&c.process().done(function(){c.submit()}),void 0)},processData:!1,contentType:!1,cache:!1},_specialOptions:["fileInput","dropZone","pasteZone","multipart","forceIframeTransport"],_blobSlice:a.support.blobSlice&&function(){var a=this.slice||this.webkitSlice||this.mozSlice;return a.apply(this,arguments)},_BitrateTimer:function(){this.timestamp=Date.now?Date.now():(new Date).getTime(),this.loaded=0,this.bitrate=0,this.getBitrate=function(a,b,c){var d=a-this.timestamp;return(!this.bitrate||!c||d>c)&&(this.bitrate=8*(b-this.loaded)*(1e3/d),this.loaded=b,this.timestamp=a),this.bitrate}},_isXHRUpload:function(b){return!b.forceIframeTransport&&(!b.multipart&&a.support.xhrFileUpload||a.support.xhrFormDataFileUpload)},_getFormData:function(b){var c;return"function"===a.type(b.formData)?b.formData(b.form):a.isArray(b.formData)?b.formData:"object"===a.type(b.formData)?(c=[],a.each(b.formData,function(a,b){c.push({name:a,value:b})}),c):[]},_getTotal:function(b){var c=0;return a.each(b,function(a,b){c+=b.size||1}),c},_initProgressObject:function(b){var c={loaded:0,total:0,bitrate:0};b._progress?a.extend(b._progress,c):b._progress=c},_initResponseObject:function(a){var b;if(a._response)for(b in a._response)a._response.hasOwnProperty(b)&&delete a._response[b];else a._response={}},_onProgress:function(b,c){if(b.lengthComputable){var e,d=Date.now?Date.now():(new Date).getTime();if(c._time&&c.progressInterval&&d-c._time<c.progressInterval&&b.loaded!==b.total)return;c._time=d,e=Math.floor(b.loaded/b.total*(c.chunkSize||c._progress.total))+(c.uploadedBytes||0),this._progress.loaded+=e-c._progress.loaded,this._progress.bitrate=this._bitrateTimer.getBitrate(d,this._progress.loaded,c.bitrateInterval),c._progress.loaded=c.loaded=e,c._progress.bitrate=c.bitrate=c._bitrateTimer.getBitrate(d,e,c.bitrateInterval),this._trigger("progress",a.Event("progress",{delegatedEvent:b}),c),this._trigger("progressall",a.Event("progressall",{delegatedEvent:b}),this._progress)}},_initProgressListener:function(b){var c=this,d=b.xhr?b.xhr():a.ajaxSettings.xhr();d.upload&&(a(d.upload).bind("progress",function(a){var d=a.originalEvent;a.lengthComputable=d.lengthComputable,a.loaded=d.loaded,a.total=d.total,c._onProgress(a,b)}),b.xhr=function(){return d})},_isInstanceOf:function(a,b){return Object.prototype.toString.call(b)==="[object "+a+"]"},_initXHRData:function(b){var d,c=this,e=b.files[0],f=b.multipart||!a.support.xhrFileUpload,g="array"===a.type(b.paramName)?b.paramName[0]:b.paramName;b.headers=a.extend({},b.headers),b.contentRange&&(b.headers["Content-Range"]=b.contentRange),f&&!b.blob&&this._isInstanceOf("File",e)||(b.headers["Content-Disposition"]='attachment; filename="'+encodeURI(e.name)+'"'),f?a.support.xhrFormDataFileUpload&&(b.postMessage?(d=this._getFormData(b),b.blob?d.push({name:g,value:b.blob}):a.each(b.files,function(c,e){d.push({name:"array"===a.type(b.paramName)&&b.paramName[c]||g,value:e})})):(c._isInstanceOf("FormData",b.formData)?d=b.formData:(d=new FormData,a.each(this._getFormData(b),function(a,b){d.append(b.name,b.value)})),b.blob?d.append(g,b.blob,e.name):a.each(b.files,function(e,f){(c._isInstanceOf("File",f)||c._isInstanceOf("Blob",f))&&d.append("array"===a.type(b.paramName)&&b.paramName[e]||g,f,f.uploadName||f.name)})),b.data=d):(b.contentType=e.type||"application/octet-stream",b.data=b.blob||e),b.blob=null},_initIframeSettings:function(b){var c=a("<a></a>").prop("href",b.url).prop("host");b.dataType="iframe "+(b.dataType||""),b.formData=this._getFormData(b),b.redirect&&c&&c!==location.host&&b.formData.push({name:b.redirectParamName||"redirect",value:b.redirect})},_initDataSettings:function(a){this._isXHRUpload(a)?(this._chunkedUpload(a,!0)||(a.data||this._initXHRData(a),this._initProgressListener(a)),a.postMessage&&(a.dataType="postmessage "+(a.dataType||""))):this._initIframeSettings(a)},_getParamName:function(b){var c=a(b.fileInput),d=b.paramName;return d?a.isArray(d)||(d=[d]):(d=[],c.each(function(){for(var b=a(this),c=b.prop("name")||"files[]",e=(b.prop("files")||[1]).length;e;)d.push(c),e-=1}),d.length||(d=[c.prop("name")||"files[]"])),d},_initFormSettings:function(b){b.form&&b.form.length||(b.form=a(b.fileInput.prop("form")),b.form.length||(b.form=a(this.options.fileInput.prop("form")))),b.paramName=this._getParamName(b),b.url||(b.url=b.form.prop("action")||location.href),b.type=(b.type||"string"===a.type(b.form.prop("method"))&&b.form.prop("method")||"").toUpperCase(),"POST"!==b.type&&"PUT"!==b.type&&"PATCH"!==b.type&&(b.type="POST"),b.formAcceptCharset||(b.formAcceptCharset=b.form.attr("accept-charset"))},_getAJAXSettings:function(b){var c=a.extend({},this.options,b);return this._initFormSettings(c),this._initDataSettings(c),c},_getDeferredState:function(a){return a.state?a.state():a.isResolved()?"resolved":a.isRejected()?"rejected":"pending"},_enhancePromise:function(a){return a.success=a.done,a.error=a.fail,a.complete=a.always,a},_getXHRPromise:function(b,c,d){var e=a.Deferred(),f=e.promise();return c=c||this.options.context||f,b===!0?e.resolveWith(c,d):b===!1&&e.rejectWith(c,d),f.abort=e.promise,this._enhancePromise(f)},_addConvenienceMethods:function(b,c){var d=this,e=function(b){return a.Deferred().resolveWith(d,b).promise()};c.process=function(b,f){return(b||f)&&(c._processQueue=this._processQueue=(this._processQueue||e([this])).pipe(function(){return c.errorThrown?a.Deferred().rejectWith(d,[c]).promise():e(arguments)}).pipe(b,f)),this._processQueue||e([this])},c.submit=function(){return"pending"!==this.state()&&(c.jqXHR=this.jqXHR=d._trigger("submit",a.Event("submit",{delegatedEvent:b}),this)!==!1&&d._onSend(b,this)),this.jqXHR||d._getXHRPromise()},c.abort=function(){return this.jqXHR?this.jqXHR.abort():(this.errorThrown="abort",d._trigger("fail",null,this),d._getXHRPromise(!1))},c.state=function(){return this.jqXHR?d._getDeferredState(this.jqXHR):this._processQueue?d._getDeferredState(this._processQueue):void 0},c.processing=function(){return!this.jqXHR&&this._processQueue&&"pending"===d._getDeferredState(this._processQueue)},c.progress=function(){return this._progress},c.response=function(){return this._response}},_getUploadedBytes:function(a){var b=a.getResponseHeader("Range"),c=b&&b.split("-"),d=c&&c.length>1&&parseInt(c[1],10);return d&&d+1},_chunkedUpload:function(b,c){b.uploadedBytes=b.uploadedBytes||0;var l,m,d=this,e=b.files[0],f=e.size,g=b.uploadedBytes,h=b.maxChunkSize||f,i=this._blobSlice,j=a.Deferred(),k=j.promise();return this._isXHRUpload(b)&&i&&(g||f>h)&&!b.data?c?!0:g>=f?(e.error=b.i18n("uploadedBytes"),this._getXHRPromise(!1,b.context,[null,"error",e.error])):(m=function(){var c=a.extend({},b),k=c._progress.loaded;c.blob=i.call(e,g,g+h,e.type),c.chunkSize=c.blob.size,c.contentRange="bytes "+g+"-"+(g+c.chunkSize-1)+"/"+f,d._initXHRData(c),d._initProgressListener(c),l=(d._trigger("chunksend",null,c)!==!1&&a.ajax(c)||d._getXHRPromise(!1,c.context)).done(function(e,h,i){g=d._getUploadedBytes(i)||g+c.chunkSize,k+c.chunkSize-c._progress.loaded&&d._onProgress(a.Event("progress",{lengthComputable:!0,loaded:g-c.uploadedBytes,total:g-c.uploadedBytes}),c),b.uploadedBytes=c.uploadedBytes=g,c.result=e,c.textStatus=h,c.jqXHR=i,d._trigger("chunkdone",null,c),d._trigger("chunkalways",null,c),f>g?m():j.resolveWith(c.context,[e,h,i])}).fail(function(a,b,e){c.jqXHR=a,c.textStatus=b,c.errorThrown=e,d._trigger("chunkfail",null,c),d._trigger("chunkalways",null,c),j.rejectWith(c.context,[a,b,e])})},this._enhancePromise(k),k.abort=function(){return l.abort()},m(),k):!1},_beforeSend:function(a,b){0===this._active&&(this._trigger("start"),this._bitrateTimer=new this._BitrateTimer,this._progress.loaded=this._progress.total=0,this._progress.bitrate=0),this._initResponseObject(b),this._initProgressObject(b),b._progress.loaded=b.loaded=b.uploadedBytes||0,b._progress.total=b.total=this._getTotal(b.files)||1,b._progress.bitrate=b.bitrate=0,this._active+=1,this._progress.loaded+=b.loaded,this._progress.total+=b.total},_onDone:function(b,c,d,e){var f=e._progress.total,g=e._response;e._progress.loaded<f&&this._onProgress(a.Event("progress",{lengthComputable:!0,loaded:f,total:f}),e),g.result=e.result=b,g.textStatus=e.textStatus=c,g.jqXHR=e.jqXHR=d,this._trigger("done",null,e)},_onFail:function(a,b,c,d){var e=d._response;d.recalculateProgress&&(this._progress.loaded-=d._progress.loaded,this._progress.total-=d._progress.total),e.jqXHR=d.jqXHR=a,e.textStatus=d.textStatus=b,e.errorThrown=d.errorThrown=c,this._trigger("fail",null,d)},_onAlways:function(a,b,c,d){this._trigger("always",null,d)},_onSend:function(b,c){c.submit||this._addConvenienceMethods(b,c);var e,f,g,h,d=this,i=d._getAJAXSettings(c),j=function(){return d._sending+=1,i._bitrateTimer=new d._BitrateTimer,e=e||((f||d._trigger("send",a.Event("send",{delegatedEvent:b}),i)===!1)&&d._getXHRPromise(!1,i.context,f)||d._chunkedUpload(i)||a.ajax(i)).done(function(a,b,c){d._onDone(a,b,c,i)}).fail(function(a,b,c){d._onFail(a,b,c,i)}).always(function(a,b,c){if(d._onAlways(a,b,c,i),d._sending-=1,d._active-=1,i.limitConcurrentUploads&&i.limitConcurrentUploads>d._sending)for(var e=d._slots.shift();e;){if("pending"===d._getDeferredState(e)){e.resolve();break}e=d._slots.shift()}0===d._active&&d._trigger("stop")})};return this._beforeSend(b,i),this.options.sequentialUploads||this.options.limitConcurrentUploads&&this.options.limitConcurrentUploads<=this._sending?(this.options.limitConcurrentUploads>1?(g=a.Deferred(),this._slots.push(g),h=g.pipe(j)):(this._sequence=this._sequence.pipe(j,j),h=this._sequence),h.abort=function(){return f=[void 0,"abort","abort"],e?e.abort():(g&&g.rejectWith(i.context,f),j())},this._enhancePromise(h)):j()},_onAdd:function(b,c){var n,o,p,q,d=this,e=!0,f=a.extend({},this.options,c),g=c.files,h=g.length,i=f.limitMultiFileUploads,j=f.limitMultiFileUploadSize,k=f.limitMultiFileUploadSizeOverhead,l=0,m=this._getParamName(f),r=0;if(!j||h&&void 0!==g[0].size||(j=void 0),(f.singleFileUploads||i||j)&&this._isXHRUpload(f))if(f.singleFileUploads||j||!i)if(!f.singleFileUploads&&j)for(p=[],n=[],q=0;h>q;q+=1)l+=g[q].size+k,(q+1===h||l+g[q+1].size+k>j||i&&q+1-r>=i)&&(p.push(g.slice(r,q+1)),o=m.slice(r,q+1),o.length||(o=m),n.push(o),r=q+1,l=0);else n=m;else for(p=[],n=[],q=0;h>q;q+=i)p.push(g.slice(q,q+i)),o=m.slice(q,q+i),o.length||(o=m),n.push(o);else p=[g],n=[m];return c.originalFiles=g,a.each(p||g,function(f,g){var h=a.extend({},c);return h.files=p?g:[g],h.paramName=n[f],d._initResponseObject(h),d._initProgressObject(h),d._addConvenienceMethods(b,h),e=d._trigger("add",a.Event("add",{delegatedEvent:b}),h)}),e},_replaceFileInput:function(b){var c=b.clone(!0);a("<form></form>").append(c)[0].reset(),b.after(c).detach(),a.cleanData(b.unbind("remove")),this.options.fileInput=this.options.fileInput.map(function(a,d){return d===b[0]?c[0]:d}),b[0]===this.element[0]&&(this.element=c)},_handleFileTreeEntry:function(b,c){var g,d=this,e=a.Deferred(),f=function(a){a&&!a.entry&&(a.entry=b),e.resolve([a])};return c=c||"",b.isFile?b._file?(b._file.relativePath=c,e.resolve(b._file)):b.file(function(a){a.relativePath=c,e.resolve(a)},f):b.isDirectory?(g=b.createReader(),g.readEntries(function(a){d._handleFileTreeEntries(a,c+b.name+"/").done(function(a){e.resolve(a)}).fail(f)},f)):e.resolve([]),e.promise()},_handleFileTreeEntries:function(b,c){var d=this;return a.when.apply(a,a.map(b,function(a){return d._handleFileTreeEntry(a,c)})).pipe(function(){return Array.prototype.concat.apply([],arguments)})},_getDroppedFiles:function(b){b=b||{};var c=b.items;return c&&c.length&&(c[0].webkitGetAsEntry||c[0].getAsEntry)?this._handleFileTreeEntries(a.map(c,function(a){var b;return a.webkitGetAsEntry?(b=a.webkitGetAsEntry(),b&&(b._file=a.getAsFile()),b):a.getAsEntry()})):a.Deferred().resolve(a.makeArray(b.files)).promise()},_getSingleFileInputFiles:function(b){b=a(b);var d,e,c=b.prop("webkitEntries")||b.prop("entries");if(c&&c.length)return this._handleFileTreeEntries(c);if(d=a.makeArray(b.prop("files")),d.length)void 0===d[0].name&&d[0].fileName&&a.each(d,function(a,b){b.name=b.fileName,b.size=b.fileSize});else{if(e=b.prop("value"),!e)return a.Deferred().resolve([]).promise();d=[{name:e.replace(/^.*\\/,"")}]}return a.Deferred().resolve(d).promise()},_getFileInputFiles:function(b){return b instanceof a&&1!==b.length?a.when.apply(a,a.map(b,this._getSingleFileInputFiles)).pipe(function(){return Array.prototype.concat.apply([],arguments)}):this._getSingleFileInputFiles(b)},_onChange:function(b){var c=this,d={fileInput:a(b.target),form:a(b.target.form)};this._getFileInputFiles(d.fileInput).always(function(e){d.files=e,c.options.replaceFileInput&&c._replaceFileInput(d.fileInput),c._trigger("change",a.Event("change",{delegatedEvent:b}),d)!==!1&&c._onAdd(b,d)})},_onPaste:function(b){var c=b.originalEvent&&b.originalEvent.clipboardData&&b.originalEvent.clipboardData.items,d={files:[]};c&&c.length&&(a.each(c,function(a,b){var c=b.getAsFile&&b.getAsFile();c&&d.files.push(c)}),this._trigger("paste",a.Event("paste",{delegatedEvent:b}),d)!==!1&&this._onAdd(b,d))},_onDrop:function(b){b.dataTransfer=b.originalEvent&&b.originalEvent.dataTransfer;var c=this,d=b.dataTransfer,e={};d&&d.files&&d.files.length&&(b.preventDefault(),this._getDroppedFiles(d).always(function(d){e.files=d,c._trigger("drop",a.Event("drop",{delegatedEvent:b}),e)!==!1&&c._onAdd(b,e)}))},_onDragOver:function(b){b.dataTransfer=b.originalEvent&&b.originalEvent.dataTransfer;var c=b.dataTransfer;c&&-1!==a.inArray("Files",c.types)&&this._trigger("dragover",a.Event("dragover",{delegatedEvent:b}))!==!1&&(b.preventDefault(),c.dropEffect="copy")},_initEventHandlers:function(){this._isXHRUpload(this.options)&&(this._on(this.options.dropZone,{dragover:this._onDragOver,drop:this._onDrop}),this._on(this.options.pasteZone,{paste:this._onPaste})),a.support.fileInput&&this._on(this.options.fileInput,{change:this._onChange})},_destroyEventHandlers:function(){this._off(this.options.dropZone,"dragover drop"),this._off(this.options.pasteZone,"paste"),this._off(this.options.fileInput,"change")},_setOption:function(b,c){var d=-1!==a.inArray(b,this._specialOptions);d&&this._destroyEventHandlers(),this._super(b,c),d&&(this._initSpecialOptions(),this._initEventHandlers())},_initSpecialOptions:function(){var b=this.options;void 0===b.fileInput?b.fileInput=this.element.is('input[type="file"]')?this.element:this.element.find('input[type="file"]'):b.fileInput instanceof a||(b.fileInput=a(b.fileInput)),b.dropZone instanceof a||(b.dropZone=a(b.dropZone)),b.pasteZone instanceof a||(b.pasteZone=a(b.pasteZone))},_getRegExp:function(a){var b=a.split("/"),c=b.pop();return b.shift(),new RegExp(b.join("/"),c)},_isRegExpOption:function(b,c){return"url"!==b&&"string"===a.type(c)&&/^\/.*\/[igm]{0,3}$/.test(c)},_initDataAttributes:function(){var b=this,c=this.options,d=a(this.element[0].cloneNode(!1));a.each(d.data(),function(a,e){var f="data-"+a.replace(/([a-z])([A-Z])/g,"$1-$2").toLowerCase();d.attr(f)&&(b._isRegExpOption(a,e)&&(e=b._getRegExp(e)),c[a]=e)})},_create:function(){this._initDataAttributes(),this._initSpecialOptions(),this._slots=[],this._sequence=this._getXHRPromise(!0),this._sending=this._active=0,this._initProgressObject(this),this._initEventHandlers()},active:function(){return this._active},progress:function(){return this._progress},add:function(b){var c=this;b&&!this.options.disabled&&(b.fileInput&&!b.files?this._getFileInputFiles(b.fileInput).always(function(a){b.files=a,c._onAdd(null,b)}):(b.files=a.makeArray(b.files),this._onAdd(null,b)))},send:function(b){if(b&&!this.options.disabled){if(b.fileInput&&!b.files){var f,g,c=this,d=a.Deferred(),e=d.promise();return e.abort=function(){return g=!0,f?f.abort():(d.reject(null,"abort","abort"),e)},this._getFileInputFiles(b.fileInput).always(function(a){if(!g){if(!a.length)return d.reject(),void 0;b.files=a,f=c._onSend(null,b).then(function(a,b,c){d.resolve(a,b,c)},function(a,b,c){d.reject(a,b,c)})}}),this._enhancePromise(e)}if(b.files=a.makeArray(b.files),b.files.length)return this._onSend(null,b)}return this._getXHRPromise(!1,b&&b.context)}})});

/*
 * CIS File Upload Plugin
 */
(function(window, undefined) {
    'use strict';

    var // Localise globals
        document = window.document,
        $ = window.$,
        CIS = window.CIS = window.CIS || {};

    CIS.FileUpload = CIS.FileUpload || {
        /**
         * Get a specific control inside a wrapper
         */
        getControl: function($e, name) {
            var $parent = $e.closest($e.data('parent')),
                $control = $parent.find($e.data(name + '-target'));

            if ($control.length === 0 &&
                $.inArray(name, ['progress', 'holder']) != -1
            ) {
                // Create the control if it does not exist
                $control = $(CIS.FileUpload.getTemplate($e, name));
                $control.appendTo($parent);
            }
            return $control;
        },
        /**
         * Get HTML template
         */
        getTemplate: function($e, name, data) {
            var $parent = $e.closest($e.data('parent')),
                html = $parent.find('.js-' + name + '-template').html();

            if (typeof data !== 'undefined') {
                // Compile template
                for (var prop in data) {
                    if (data.hasOwnProperty(prop)) {
                        html = html.replace(new RegExp('{{' + prop + '}}', 'g'), data[prop]);
                    }
                }
            }

            return html;
        },
		/**
         * Save avatar image to database
         */
        saveAvatar: function($e, name, url){
        	$.ajax({
                url: url,
                type: "POST",
                crossDomain: true,
                data: {
                    'pic' : name
                }
            });
            return true;
    	},
        /**
         * Setup photo upload control
         */
        init: function($e, data) {
            var defaultData = {
                dataType: 'json',
                formData: {}
            };
            data = $.extend(defaultData, data);
            var successCallback = data.successCallback;

            var uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });

            $e.fileupload({
                url: data.url,
                formData: data.formData,
                dataType: data.dataType,
                //autoUpload: false,
                start: function(e, data) {
                    // Show progress bar
                    CIS.FileUpload.getControl($(this), 'progress').removeClass('hide');
                },
                progressall: function(e, data) {
                    // Update progress bar
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    CIS.FileUpload.getControl($(this), 'progress').find('.progress-bar').css({
                        width: progress + '%'
                    });
                },
                done: function(e, data) {
                    var _this = this,
                        $this = $(_this),
                        $holder = CIS.FileUpload.getControl($this, 'holder');
                    $.each(data.result.files, function (index, file) {
                        if (file.url) {
                            // Upload successful
                            var imageHolderTarget = $this.data('image-holder-target');
                            if (imageHolderTarget !== undefined) {
                                // Replace the holder with the newly uploaded item
                                $(imageHolderTarget).replaceWith(CIS.FileUpload.getTemplate($this, 'item', file));
                            } else {
                                if (typeof $this.attr('multiple') !== 'undefined') {
                                    // Holder contains all uploaded items
                                    $holder.append(CIS.FileUpload.getTemplate($this, 'item', file));
                                } else {
                                    // Holder only contains the latest uploaded item
                                    $holder.html(CIS.FileUpload.getTemplate($this, 'item', file));
                                }
                            }

                            if (typeof successCallback === 'function') {
                                successCallback.call(_this, file);
                            }
                        } else if (file.error) {
                            // Append the error upload message (file.error) to the holder
                            $holder.append(CIS.FileUpload.getTemplate($this, 'error', file));
                        }
                    });
                },
                fail: function(e, data) {
                    // Append the error upload message (data.errorThrown) to the holder
                    var $this = $(this),
                        $holder = CIS.FileUpload.getControl($(this), 'holder');
                    $.each(data.files, function (index, file) {
                        $holder.append(CIS.FileUpload.getTemplate($this, 'error', {
                            name: file.name,
                            error: data.errorThrown
                        }));
                    });
                }
            }).prop('disabled', ! $.support.fileInput);
			// .on('fileuploadadd', function (e, data) {
		 //        data.context = $('<div/>').appendTo('#files');
		 //        $.each(data.files, function (index, file) {
		 //            var node = $('<p/>')
		 //                    .append($('<span/>').text(file.name));
		 //            if (!index) {
		 //                node
		 //                    .append('<br>')
		 //                    .append(uploadButton.clone(true).data(data));
		 //            }
		 //            node.appendTo(data.context);
		 //        });
		 //    });
        }
    };
})(window);