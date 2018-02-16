<?
require_once("common.inc.php");
?>
var currentLang='<?=$_SESSION['lang']?>';
var translateLangs=new Array();
<?
    $x=0;
    foreach($config['languages'] AS $l=>$ln) {
        if($l==$_SESSION['lang']) continue;
        echo "translateLangs[$x]=\"$l\";\n";
        $x++;
    }
?>

$(document).ready(function() {
    //add a link right after any element that has the class "translatable"
    $(".translatable").each( function(i) {
        $(this).after(" <a href=\"#translate\"><?=i18n("translations")?></a>");
    });

    //add a click handler to the previously added link
    $("[href*=#translate]").click(function() {
        popup_translator($(this).prev().val());
        return false;
    });

    //initialize the dialog
    $("#translation_editor").dialog({
        bgiframe: true, autoOpen: false,
        modal: true, resizable: false,
        draggable: false
    });
});

function popup_translator(str) {
    var w = (document.documentElement.clientWidth * 0.6);
    var h = (document.documentElement.clientHeight * 0.4);

    $('#translation_editor').dialog('option','width',w);
    $('#translation_editor').dialog('option','height',h);
    $('#translation_editor').dialog('option','buttons',{ "<?=i18n("Save")?>": function() { save_translations(); }, 
                                                         "<?=i18n("Cancel")?>": function(){ $(this).dialog("close");}});
    $('#translation_editor').dialog('open');

    $('#translate_str').html(str);
    $('#translate_str_hidden').val(str);

    $.getJSON("<?=$config['SFIABDIRECTORY']?>/admin/gettranslation.php?str="+escape(str),function(json){
        for(var i=0;i<translateLangs.length;i++) {
            $("#translate_"+translateLangs[i]).val(json[translateLangs[i]]);
        }
    });

    return false;
}

function save_translations() {
    $.post("<?=$config['SFIABDIRECTORY']?>/admin/settranslation.php",
    $("#translationform").serialize(),
    function(data) {
        $('#translation_editor').dialog('close');
    });
    return false;
}
