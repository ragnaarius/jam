var fual_show_popin = true;

function fualEditAlias(id_article, e) {
	if(fual_show_popin) {
		var title = document.getElementById('fual_' + id_article + '_title').value;
		var alias = document.getElementById('fual_' + id_article + '_alias').value;

		//don't use internet explorer
		var x = (e.layerX === undefined) ? e.x : e.layerX;
		var y = (e.layerY === undefined) ? e.y : e.layerY;
	
		var ie = (/MSIE (\d+\.\d+);/.test(navigator.userAgent));
		if(ie) {
			var iev = new Number(RegExp.$1);
			if(iev == 7) {
				y += document.documentElement.scrollTop;
			}
		}
	
		fualCreateAliasForm(id_article, title, alias, x, y);
	}
}

function fualCreateAliasForm(id_article, title, alias, x, y) {
	fualfeafEnableButtons();
	var div = document.getElementById('fual_edit_alias_form');

	//div.style.top = y + 'px';
	//div.style.left = (x-parseInt(div.style.width)) + 'px';

	document.getElementById('feaf_id_article').innerHTML = id_article;
	document.getElementById('feaf_title').innerHTML = title;
	document.getElementById('feaf_alias').value = alias;

	div.style.display = 'block';
	document.getElementById('feaf_alias').focus();
}

function fualCloseAliasForm() {
	var div = document.getElementById('fual_edit_alias_form');
	div.style.display = 'none';
    document.getElementById('alert-block').style.display = 'none';
	fual_show_popin = true;
}

function fualSaveAlias() {
	if(fualValidateAlias()) {
		var httpRequest;
		var url = 'index.php?option=com_jam&task=saveAlias';
		var parametros = 'id_article=' + document.getElementById('feaf_id_article').innerHTML;
		parametros += '&alias=' + document.getElementById('feaf_alias').value;
		if(window.XMLHttpRequest) { // Mozilla, etc, ...
			httpRequest = new XMLHttpRequest();
			if (httpRequest.overrideMimeType) {
				httpRequest.overrideMimeType("text/html");
			}
		}
		else if(window.ActiveXObject) { // IE(ca)
			try {
				httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e) {}
			}
		}
		if (!httpRequest) {
			alert("Oops! ;(");
			return false;
		}
		httpRequest.onreadystatechange = function() {
			if(httpRequest.readyState == 4){
				if(httpRequest.status == 200){
					var retorno = httpRequest.responseText;
					
					if(retorno == "ok") {
						 //alert(document.getElementById('feaf_txt_ok_save').value);
                        document.getElementById('alert-block').style.display = 'block';
                        document.getElementById('alert-block').innerHTML = document.getElementById('feaf_txt_ok_save').value;
                        //fualfeafCloseButtons()
                        fualChangeImgAliasTitle();
                        fualCloseAliasForm();
					}
					else {
						//alert(document.getElementById('feaf_txt_error_save').value);
                        document.getElementById('alert-block').style.display = 'block';
                        document.getElementById('alert-block').innerHTML = document.getElementById('feaf_txt_error_save').value;
						fualfeafEnableButtons();
					}
				}
			}
		};
		httpRequest.open("POST", url, true);
		httpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		httpRequest.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
		httpRequest.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
		httpRequest.setRequestHeader("Pragma", "no-cache");
		httpRequest.send(parametros);
	}
}

function fualfeafDisableButtons() {
	fual_show_popin = false;
	document.getElementById('feaf_bt_save').innerHTML = document.getElementById('feaf_txt_saving').value;
	document.getElementById('feaf_bt_save').disabled = true;
	document.getElementById('feaf_bt_cancel').disabled = true;
}

function fualfeafCloseButtons() {
	fual_show_popin = true;
	document.getElementById('feaf_bt_cancel').innerHTML = document.getElementById('feaf_txt_close').value;
	document.getElementById('feaf_bt_cancel').disabled = false;
    document.getElementById('feaf_bt_save').parentNode.removeChild(document.getElementById('feaf_bt_save'));
}

function fualfeafEnableButtons() {
	fual_show_popin = true;
	document.getElementById('feaf_bt_save').innerHTML = document.getElementById('feaf_txt_save').value;
	document.getElementById('feaf_bt_save').disabled = false;
	document.getElementById('feaf_bt_cancel').disabled = false;
}

function fualValidateAlias() {
	fualfeafDisableButtons();
	var regex1 = new RegExp("^[a-z0-9-]+$");
	var regex2 = new RegExp("-{2,}");
	var alias = document.getElementById('feaf_alias');

	if(!regex1.test(alias.value) || regex2.test(alias.value)) {
		//alert(document.getElementById('feaf_txt_error').value);
        document.getElementById('alert-block').style.display = 'block';
        document.getElementById('alert-block').innerHTML = document.getElementById('feaf_txt_error').value;
		document.getElementById('feaf_alias').focus();
		fualfeafEnableButtons();
		return false;
	}
	return true;
}

function fualChangeImgAliasTitle() {
	if(!fual_show_popin) {
		var new_title = document.getElementById('feaf_txt_edit_alias').value + ' :: ' + document.getElementById('feaf_alias').value;
		var id_article = document.getElementById('feaf_id_article').innerHTML;
		document.getElementById('img_alias_'+id_article).title = new_title;
		document.getElementById('fual_'+id_article+'_alias').value = document.getElementById('feaf_alias').value;
	}
}


