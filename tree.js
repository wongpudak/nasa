var K0=window,Q1=document,A2=write,O2=value;
function collpase(id,O2){
	Q1.getElementById(id).style.display="none";
	var str='symbol_'+id.replace(/_/g,'');
	if(Q1.getElementById(str)){
	var mainid=id.replace(/_/g,'');
	var symbolhref='<span id="symbol_'+mainid+'"><a href="javascript:expand(\''+id+'\','+O2+');" class="nodecls">'+UI.expand+'</a></span>';
	Q1.getElementById(str).innerHTML=symbolhref;}
}
function expand(id,O2){
	loadChild(id,O2);
	Q1.getElementById(id).style.display="block";
	var str='symbol_'+id.replace(/_/g,'');
	if(Q1.getElementById(str)){
		var mainid=id.replace(/_/g,'');
		var symbolhref='<span id="symbol_'+mainid+'"><a href="javascript:collpase(\''+id+'\','+O2+');" class="nodecls">'+UI.collapse+'</a></span>';
		Q1.getElementById(str).innerHTML=symbolhref;
	}
}
function loadChild(id,O2){
	var strParam="../wp-content/plugins/wp-affiliasi/services.php?method=getCat&id="+id+"&catid="+O2;
	Ajax.Request(strParam,generateChild);
	}
function generateChild(){
	Ajax.setShowMessage(1);
	Ajax.setMessage("Loaded..");
	if(Ajax.CheckReadyState(Ajax.request)){
		var	response=eval('('+Ajax.request.responseText+')');
		var str='';
		var i=0;
		if(response.data.length==0){
			Q1.getElementById(response.id).style.display="none";
		}
		var mainid=response.id.replace(/_/g,'');
		for(i=0;i < response.data.length;i++){
			str+='<div id="'+mainid+''+i+'" style="padding-left:20px;">';
			str+='<span id="symbol_'+mainid+''+i+'"><a href="javascript:expand(\''+response.id+'_'+i+'\','+response.data[i].id+');" class="nodecls">'+UI.expand+'</a></span>';
			str+='<a href="javascript:getData('+response.data[i].id+');" class="nodecls">'+response.data[i].name+'</a></div>';
			str+='<div id="'+response.id+'_'+i+'" style="padding-left:20px;display:none"></div>';
		}
		Q1.getElementById(response.id).innerHTML=str;
	}
}