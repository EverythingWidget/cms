<?php
session_start();
include($_SESSION['ROOT_DIR'] . '/config.php');
?>
<span class="Row">
    <span class="Label" style="width: 110px; text-align: left;">
        انتخاب شاخه:
    </span>
    <span class="button Blue" style="float: right" onclick="selectLink('path')">-</span>
    <input type="hidden" id="path">
</span>

<script  type="text/javascript">    
    function selectLink(elmId)
    {
        $("#TopContentPane").fadeIn(200, function(){
            $("#TCPC").html("<span class='LoadingAnimation'></span>");
            $("#TCPC").fadeIn(400);
            $.post('Tools/LinkChooser.php', {elementId: elmId}, function(data){                
                $("#TCPC").html(data);                
            });
        });       
    }
    
    function afterSelectPath(elmId)
    {
        if($("#path").val().indexOf("category") >= 0)
            setValueInParameters("#parameters" , "categoryId" , $("#path").val().substring($("#path").val().indexOf("category")+9));
        else
            alert("لطفا از شاخه ها انتخاب کنید");
    }
    
</script>
