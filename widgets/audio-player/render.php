<audio>
  <source src="~rm/public/media/audio-44/mpthreetest.mp3" type="audio/mp3">
  Your browser does not support the audio element.
</audio>
<script>
  window.addEventListener('load', function () {
    audiojs.events.ready(function () {
      var as = audiojs.createAll();
    });
  });
</script>