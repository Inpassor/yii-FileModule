/**
 * CAjaxUpload.js
 * @version 0.0.1 (2015.09.23)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

;(function($,window,document,undefined){

	var pluginName='CAjaxUpload',
	defaults={
		beforeSend:null,
		context:null,
		data:{},
		done:null,
		dataFilter:null,
		dataType:'json',
		loadingClass:'loading',
		loadingClassContainer:'input[type="submit"]',
		loadingClassContext:'this',
		error:null,
		errorClass:'error',
		errorMessageClass:'errorMessage',
		success:null,
		timeout:null,
		focus:null,
		inputHidden:true,
		progressSelector:'progress',
		progressContext:'this',
		progressHtmlInitial:'',
		maxFiles:1,
		maxSize:3145728,
		types:[]
	},
	Plugin=function(element,options){
		this.element=element;
		this.options=$.extend({},true,defaults,window[this.name+'Defaults']||{},options);
		this.name=pluginName;
		this.defaults=defaults;
		this.init();
	};

	Plugin.prototype={
		_action:'',
		_method:'post',
		error:function(errors){
			if (!errors||!errors instanceof Array) {
				return;
			}
			for (var i in errors) {
				if (!$('#'+i+'_em_',this.element).length) {
					continue;
				}
				$('#'+i+'_em_',this.element)
					.html(errors[i] instanceof Array?errors[i].join('<br/>'):errors[i])
					.parent()
					.addClass(this.options.errorClass);
			}
		},
		init:function(){
			if (this.options.loadingClassContext==='this') {
				this.options.loadingClassContext=this.element;
			}
			if (this.options.progressContext==='this') {
				this.options.progressContext=this.element;
			}
			if ($(this.element).attr('action')) {
				this._action=$(this.element).attr('action');
			}
			if ($(this.element).attr('method')) {
				this._method=$(this.element).attr('method');
			}
			var $this=this;
			$('input[type="file"]',this.element).on('change',function(event){
				event.preventDefault();
				var name=$(this).attr('name'),
				data=new FormData(),
				countFiles=0;
				_tmpData=$($this.element).serializeArray();
				for (var i=0,l=_tmpData.length;i<l;i++) {
					data.append(_tmpData[i].name,_tmpData[i].value);
				}
				for (var i=0,l=this.files.length;i<l;i++) {
					var file=this.files[i];
					if ($this.options.types&&$this.options.types instanceof Array) {
						var extensions=file.name.split('.')
					}
					if ($this.options.maxSize&&file.size>$this.options.maxSize) {
						$this.error(['Размер файла "'+file.name+'" превышает максимально допустимый.']);
						return;
					}
					data.append(name+'[]',file);
					countFiles++;
				}
				if ($this.options.maxFiles&&countFiles>$this.options.maxFiles) {
					$this.error(['Допустима одновременная загрузка файлов не более, чем '+$this.options.maxFiles+'.']);
					return;
				}
				for (var name in $this.options.data) {
					data.append(name,$this.options.data[name]);
				};
				var ajaxOptions={
					type:$this._method,
					data:data,
					cache:false,
					contentType:false,
					processData:false,
					xhr:function(){
						var req;
						try {
							req=new ActiveXObject('Msxml2.XMLHTTP');
						} catch(e) {
							try {
								req=new ActiveXObject('Microsoft.XMLHTTP');
							} catch(E) {
								req=false;
							}
						}
						if (!req&&typeof XMLHttpRequest!==undefined) {
							req=new XMLHttpRequest();
						}
						if (req.upload) {
							req.upload.addEventListener('progress',function(e){
								if (e.lengthComputable) {
									$($this.options.progressSelector,$this.options.progressContext).attr({
										value:e.loaded,
										max:e.total
									});
								}
							});
						}
						return req;
					}
				};
				if ($this.options.context) {
					ajaxOptions.context=$this.options.context;
				}
				if ($.isFunction($this.options.done)) {
					ajaxOptions.done=$this.options.done;
				}
				if ($.isFunction($this.options.dataFilter)) {
					ajaxOptions.dataFilter=$this.options.dataFilter;
				}
				if ($this.options.dataType) {
					ajaxOptions.dataType=$this.options.dataType;
				}
				if ($.isFunction($this.options.error)) {
					ajaxOptions.error=$this.options.error;
				}
				if ($this.options.timeout) {
					ajaxOptions.timeout=$this.options.timeout;
				}
				ajaxOptions.beforeSend=function(jqXHR,settings){
					if ($.isFunction($this.options.beforeSend)&&$this.options.beforeSend.call($this)===false) {
						return;
					}
					$('.'+$this.options.errorClass,$this.element).removeClass($this.options.errorClass);
					$('.'+$this.options.errorMessageClass,$this.element).html('');
					if ($this.options.loadingClass) {
						$($this.options.loadingClassContainer,$this.options.loadingClassContext).addClass($this.options.loadingClass);
					}
				}
				ajaxOptions.success=function(data,textStatus,jqXHR){
					if ($this.options.loadingClass) {
						$($this.options.loadingClassContainer,$this.options.loadingClassContext).removeClass($this.options.loadingClass);
					}
					if (data.errors) {
						$this.error(data.errors);
					} else {
						$('.'+$this.options.errorClass,$this.element).removeClass($this.options.errorClass);
						$('.'+$this.options.errorMessageClass,$this.element).html('');
					}
					if ($.isFunction($this.options.success)) {
						$this.options.success.call($this,data);
					}
				}
				ajaxOptions.complete=function(){
					$($this.options.progressSelector,$this.options.progressContext).html($this.options.progressHtmlInitial);
					$($this.element)[0].reset();
				};
				$.ajax($this._action,ajaxOptions);
			});
			if (this.options.inputHidden) {
				$(':not(input[type="file"])',this.element).on('click',function(event){
					event.preventDefault();
					$('input[type="file"]',$this.element).click();
				});
			}
			return this;
		}
	};

	$.fn[pluginName]=function(options){
		return this.each(function(){
			if (!$.data(this,pluginName)) {
				$.data(this,pluginName,new Plugin(this,options));
			}
		});
	};

})(jQuery,window,document);
