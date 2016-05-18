<div class="header-pane   row">
  <h1  class="col-xs-12">
    tr{Upload File}
  </h1>
</div>
<div  class="form-content " >
  <form enctype="multipart/form-data" id="upload-form">   
    <div class="col-xs-12 mt" id="choose-files">
      <label>Choose your files:</label>
      <input class="text-field" type="file" accept="image/*" multiple name="img[]" id="image" />
    </div>
    <input type="hidden"   name="parent_id" id="parent_id" value="<?php echo $_REQUEST["parentId"] ?>" />
    <div id="progress-bar">
      <progress value="0" max="100"></progress>
    </div>
  </form>
</div>
<div class="footer-pane actions-bar action-bar-items" >

</div>
<style>
  progress[value] {
    /* Get rid of the default appearance */
    appearance: none;

    /* This unfortunately leaves a trail of border behind in Firefox and Opera. We can remove that by setting the border to none. */
    border: none;

    /* Add dimensions */
    display: block;
    width:98%;
    height: 5px;

    /* Although firefox doesn't provide any additional pseudo class to style the progress element container, any style applied here works on the container. */
    background-color: whiteSmoke;
    border-radius: 3px;

    /* Of all IE, only IE10 supports progress element that too partially. It only allows to change the background-color of the progress value using the 'color' attribute. */
    color: royalblue;

    position: relative;
    margin: 1%; 
  }

  /*
  Webkit browsers provide two pseudo classes that can be use to style HTML5 progress element.
  -webkit-progress-bar -> To style the progress element container
  -webkit-progress-value -> To style the progress element value.
  */

  progress[value]::-webkit-progress-bar {
    background-color: whiteSmoke;
    border-radius: 3px;
    box-shadow: 0 1px 3px rgba(0,0,0,.3) inset;
  }

  progress[value]::-webkit-progress-value {
    background-color: #3cf;
    position: relative;
    border-radius:3px;

  }
</style>
<script>
  function Upload() {
    this.bUpload = EW.addAction("Upload", function () {
      uploadForm.doUpload();
    });
  }

  Upload.prototype.doUpload = function () {
    // Get the form data. This serializes the entire form. pritty easy huh!
    var form = new FormData($('#upload-form')[0]);
    uploadForm.bUpload.comeOut(200);
    $('#choose-files').animate({opacity: 0}, 200);

    // Make the ajax call
    $.ajax({
      url: '<?php echo EW_ROOT_URL ?>~admin/api/content-management/upload-file',
      type: 'POST',
      dataType: "json",
      xhr: function () {
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) {
          myXhr.upload.addEventListener('progress', uploadForm.progress, false);
        }
        return myXhr;
      },
      //add beforesend handler to validate or something
      //beforeSend: functionname,
      success: function (response) {
        $("body").EW().notify(response).show();
        if (response.status === "success") {
          $.EW("getParentDialog", $("#upload-form")).trigger("close");
          $(document).trigger("media-list.refresh");
        }
        //$('#content_here_please').html(res);
      },
      //add error handler for when a error occurs if you want!
      //error: errorfunction,
      data: form,
      // this is the important stuf you need to overide the usual post behavior
      cache: false,
      contentType: false,
      processData: false
    });
  };


  // Yes outside of the .ready space becouse this is a function not an event listner!
  Upload.prototype.progress = function (e) {
    if (e.lengthComputable) {
      //this makes a nice fancy progress bar
      $('progress').attr({value: e.loaded, max: e.total});
    }
  };

  Upload.prototype.dispose = function () {
    uploadForm.bUpload.remove();
  };
  var uploadForm = new Upload();
</script>