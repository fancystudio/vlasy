<script>
jQuery(document).ready(function($) {
  jQuery.rsCSS3Easing.easeOutBack = 'cubic-bezier(0.175, 0.885, 0.320, 1.275)';
  $('#slider-with-blocks-1').royalSlider({
    arrowsNav: true,
    arrowsNavAutoHide: false,
    fadeinLoadedSlide: false,
    controlNavigationSpacing: 0,
    controlNavigation: 'bullets',
    imageScaleMode: 'none',
    imageAlignCenter:false,
    blockLoop: true,
    loop: true,
    numImagesToPreload: 6,
    transitionType: 'fade',
    keyboardNavEnabled: true,
    block: {
      delay: 400
    }
  });
});
</script>

<div class="wrapper-slider">
 <div id="slider-with-blocks-1" class="royalSlider rsMinW">
 
  <div class="rsContent slide1">
    <div class="bContainer container">
      <span class="rsABlock txtCent blockHeadline dlhsie-vlasy" data-move-effect="left" data-speed="1000"><img src="{$base_dir}themes/evlasy/img/evlasy/dlhsie-vlasy.png" width"404" height"75"/></span>
      <span class="rsABlock txtCent plne-objemu" data-move-effect="left" data-speed="2000"><img src="{$base_dir}themes/evlasy/img/evlasy/plne-objemu.png" width"326" height"63"/></span>
      <span class="rsABlock txtCent ako-nikdy" data-move-effect="none" data-speed="3000"><img src="{$base_dir}themes/evlasy/img/evlasy/ako-nikdy-predtym.png" width"271" height"33"/></span>
      
    </div>
  </div>
  <!--<div class="rsContent slide2">
    <div class="bContainer">
      <strong class="rsABlock txtCent blockSubHeadline" data-move-effect="none">Transition Types</strong>
      <span class="rsABlock txtCent" data-move-effect="top">from top  ↓</span>
      <span class="rsABlock txtCent" data-move-effect="bottom">from bottom ↑</span>
      <span class="rsABlock txtCent" data-move-effect="left">from left →</span>
      <span class="rsABlock txtCent" data-move-effect="right">from right ←</span>
      <span class="rsABlock txtCent" data-move-effect="none">just fade</span>
    </div>
  </div>
  <div class="rsContent slide3">
    <div class="bContainer">
      <strong class="rsABlock txtCent blockSubHeadline" data-move-effect="none" data-delay="0">Customizable Animation</strong>
      <span class="rsABlock txtCent" data-move-effect="left" data-delay="1000" data-move-offset="500" data-easing="easeOutBack" data-fade-effect="none">easing</span>
      <span class="rsABlock txtCent" data-move-effect="left" data-delay="1500" data-move-offset="500" data-fade-effect="none">delay</span>
      <span class="rsABlock txtCent" data-move-effect="left" data-delay="2000" data-move-offset="500" data-speed="1000" data-fade-effect="none">speed</span>
      <span class="rsABlock txtCent" data-move-effect="left" data-delay="2500" data-move-offset="50" data-fade-effect="true">move offset</span>
    </div>
  </div>
  <!--<div class="rsContent slide4">
    <a class="rsImg" href="../img/palmbw.jpg">palms &amp; beach</a>
    <div class="bContainer">
      <strong class="rsABlock txtCent blockHeadline">Block may have any HTML</strong>
      <span class="rsABlock txtCent" data-move-effect="none">and can be placed on any slide type</span>
    </div>
    <img class="rsABlock palmImg" data-fade-effect="none" data-move-effect="bottom" data-opposite="true" data-move-offset="450" data-delay="350" data-speed="300" data-easing="easeOutBack" src="../img/palms.png" data-rsw="707" data-rsh="471">
    <div class="photoCopy">Photo by <a href="http://photo.aphecetche.fr/">Laurent Aphecetche</a></div>
  </div>  -->

  
</div>


</div><!-- wrapper slider -->
<div class="container icons-baner">
<img src="{$base_dir}themes/evlasy/img/evlasy/icon-100-human-hair.jpg" width"194" height"68"/>
<img src="{$base_dir}themes/evlasy/img/evlasy/icon-vsetko-skladom.jpg" width"194" height"68"/>
<img src="{$base_dir}themes/evlasy/img/evlasy/icon-expresne-dorucenie.jpg" width"194" height"68"/>
</div><!-- end container-->
  