//useful function that we'll be using throughout
function confirmClick(msg)
{
	var okay=confirm(msg);
	if(okay)
        	return true; 
        else 
		return false;
}

function el(str,domain,name)
{
	document.write('<a href="ma'+'il'+'to:' + str + '@' + domain + '">' + name + '</a>');
}

function em(str,domain)
{
	document.write('<a href="ma'+'il'+'to:' + str + '@' + domain + '">' + str + '@' + domain + '</a>');
}

var anyFieldHasBeenChanged=false;

function fieldChanged()
{
	anyFieldHasBeenChanged=true;
}

function confirmChanges()
{
	if(anyFieldHasBeenChanged)
	{
		var okay=confirm('<?=i18n("You have unsaved changes.  Click \"Cancel\" to return so you can save your changes, or press \"OK\" to discard your changes and continue")?>');
		if(okay)
			return true;
		else
			return false;
	}
	else
		return true;
}

var _notice_id = 0;
function notice_delete(id)
{
	$("#notice_"+id).slideUp('slow', function() {
			$("#notice_"+id).remove();
	});
}

function notice_create(type,str,timeout)
{
	if(timeout == -1) timeout = 5000;
	_notice_id++;
	$("#notice_area").append("<div id=\"notice_"+_notice_id+"\" class=\""+type+"\" >"+str+"</div>");
	$("#notice_"+_notice_id).show('puff');
	$("#notice_"+_notice_id).fadeTo('fast', 0.95);
	setTimeout("notice_delete("+_notice_id+")", timeout);
}

function notice_(str) 
{
	notice_create('notice',str,-1);
}

function report_gen(id) {
	var args="id="+id+"&action=dialog_gen";
	$("#debug").load("reports_gen.php?"+args);
	return false;
}

/* Stuff to do after the document loads */
$(document).ready(function() 
{
	/* Do label/input styles on all edit tables */
	$(".editor tr td:first-child").addClass('label');
	$(".editor tr td:nth-child(2)").addClass('input');

	/* Stripe tableviews */
	$(".tableview tr:even").addClass('even');
	$(".tableview tr:odd").addClass('odd');
});

//key is 'val' from emails table, or id is id, fcid simply gets passed in and saved if needed
//only id or key are used to lookup which to open
function opencommunicationeditor(key,id,fcid) {
	var fcstr="";
	if(fcid) 
		fcstr="&fundraising_campaigns_id="+fcid;

	if(id) {
		$("#debug").load("communication.php?action=dialog_edit&id="+id+fcstr,null,function() {
		});
	} else {
		$("#debug").load("communication.php?action=dialog_edit&key="+key+fcstr,null,function() {
		});
	}
	return false;
}


