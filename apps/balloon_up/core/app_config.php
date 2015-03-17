<?php ?>


<script  type="text/javascript">
  var balloonUp = (function() {
    function balloonUp()
    {
      this.bSave = EW.addAction("Save Changes", this.saveConfig).addClass("btn-success").hide().comeIn(300);
    }

    
    return new balloonUp();

  })();

  //balloonUp.readConfig();
</script>