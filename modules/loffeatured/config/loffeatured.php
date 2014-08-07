<?php
if (!defined('_CAN_LOAD_FILES_'))
	exit;
 
?>
<link rel="stylesheet" href="<?php echo __PS_BASE_URI__."modules/".$this->name."/assets/admin/form.css";?>" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="<?php echo __PS_BASE_URI__."modules/".$this->name."/assets/admin/form.js";?>"></script>

<?php  $yesNoLang = array("0"=>$this->l('No'),"1"=>$this->l('Yes')); ?>
<h3><?php echo $this->l('Lof Featured Products Configuration');?></h3>
<form action="<?php echo $_SERVER['REQUEST_URI'].'&rand='.rand();?>" method="post" id="lofform">
 <input type="submit" name="submit" value="<?php echo $this->l('Update');?>" class="button" />
  <fieldset>
    <legend class="lof-legend"><img src="../img/admin/contact.gif" /><?php echo $this->l('Global Setting'); ?></legend>
    <div class="lof_config_wrrapper clearfix">
      <ul>
        <?php 
            $optionsTheme = array(
                'default'=>$this->l('Default')
            );
            echo $this->_params->selectTag("module_theme",$optionsTheme,$this->getParamValue("module_theme","default"),$this->l("Theme - Styles"),'class="inputbox"', 'class="row" title="'.$this->l('Select a theme').'"');     
        ?>      	                   	        
          
           
        <li class="row module_group-product">
            <?php
               $arrOder = array('p.date_add'=>$this->l('Date Add'),'p.date_add DESC'=>$this->l('Date Add DESC'),
                                'name'=>$this->l('Name'),'name DESC'=>$this->l('Name DESC'),
                                'quantity'=>$this->l('Quantity'),'quantity DESC'=>$this->l('Quantity DESC'),
                                'p.price'=>$this->l('Price'),'p.price DESC'=>$this->l('Price DESC'));
               echo $this->_params->selectTag("order_by",$arrOder,$this->getParamValue("order_by","p.date_add"),$this->l('Order By'),'class="inputbox select-group"','','class="row home_sorce-selectmanu"');			   
               echo $this->_params->inputTag("limit_items",$this->getParamValue("limit_items","12"),$this->l('Limit items'),'class="text_area"','','class="home_sorce-selectcat"');
               
               echo $this->_params->inputTag("scroll_items",$this->getParamValue("scroll_items","1"),$this->l('Scroll items'),'class="text_area"','','class="home_sorce-selectcat"');
               
               echo $this->_params->inputTag("slide_height",$this->getParamValue("slide_height",'auto'),$this->l('Slide Height'),'class="text_area"','','class="row slider_layout-image-description"');
               echo $this->_params->inputTag("slide_width",$this->getParamValue("slide_width",'auto'),$this->l('Slide Width'),'class="text_area"','','class="row slider_layout-image-description"');

               //echo $this->_params->inputTag("speed",$this->getParamValue("speed",800),$this->l('Slider Speed'),'class="text_area"','','class="row slider_layout-image-description"');
               echo $this->_params->radioBooleanTag("auto_play", $yesNoLang,$this->getParamValue("auto_play",0),$this->l('Auto Play Slider'),'class="select-option"','class="row"','','');               			   	                            
               ?>                    
      </ul>
    </div>
  </fieldset>
    
  <fieldset>
    <legend class="lof-legend"><img src="../img/admin/contact.gif" /><?php echo $this->l('Main Slider Setting'); ?></legend>
    <div class="lof_config_wrrapper clearfix">
      <ul>
        <?php 
            $options = array(
                '_blank'=>$this->l('New Window'),
                '_self'=>$this->l('Same Window')
            );
            echo $this->_params->selectTag("target",$options,$this->getParamValue("target","_self"),$this->l("Open Link"),'class="inputbox"','class="row"','','');
            echo $this->_params->inputTag("des_max_chars",$this->getParamValue("des_max_chars","100"),$this->l('Description Max Chars'),'class="text_area"','class="row cre_main_size-"','');
            echo $this->_params->radioBooleanTag("cre_main_size", $yesNoLang, $this->getParamValue("cre_main_size", 0),$this->l('Create Size of Main Image'),'class="select-option"','class="row"','',$this->l("You can create a new size or use available size."));
            
            $mainImgSize = array();
            foreach ($formats as $k=> $format):
                $mainImgSize[$format['name']] = $format['name'].'('.$format['width']."x".$format['height'].')';
            endforeach;
            echo $this->_params->selectTag("main_img_size",$mainImgSize, $this->getParamValue("main_img_size","home"),$this->l('Main Image Size'),'class="inputbox select-group"','class="row cre_main_size-0"','',$this->l("You can create a new size via Menu <b>Preferences/Image</b>."));
            echo $this->_params->inputTag("main_height",$this->getParamValue("main_height",130),$this->l('Main Image Height'),'class="text_area"','class="row cre_main_size-1"','');
            echo $this->_params->inputTag("main_width",$this->getParamValue("main_width",100),$this->l('Main Image Width'),'class="text_area"','class="row cre_main_size-1"','');
            echo $this->_params->inputTag("limit_cols",$this->getParamValue("limit_cols",4),$this->l('Limit Colums'),'class="text_area"','class="row cre_main_size-"','');
            //echo $this->_params->inputTag("limit_rows",$this->getParamValue("limit_rows",1),$this->l('Limit Rows'),'class="text_area"','class="row cre_main_size-"','');
            echo $this->_params->radioBooleanTag("show_desc", $yesNoLang,$this->getParamValue("show_desc",1),$this->l('Enable Main Description'),'class="select-option"','class="row"','','');
            echo $this->_params->radioBooleanTag("show_price", $yesNoLang,$this->getParamValue("show_price",1),$this->l('Enable Main Price'),'class="select-option"','class="row"','','');
            echo $this->_params->radioBooleanTag("price_special", $yesNoLang,$this->getParamValue("price_special",1),$this->l('Enable Price without Reduction'),'class="select-option"','class="row"','','');
            echo $this->_params->radioBooleanTag("show_title", $yesNoLang,$this->getParamValue("show_title",1),$this->l('Enable Main Title'),'class="select-option"','class="row"','','');
            echo $this->_params->radioBooleanTag("show_image", $yesNoLang,$this->getParamValue("show_image",1),$this->l('Enable Main Image'),'class="select-option"','class="row"','','');

            
            echo $this->_params->radioBooleanTag("show_button", $yesNoLang,$this->getParamValue("show_button",1),$this->l('Enable Button Navigation'),'class="select-option"','class="row"','','');
			      echo $this->_params->radioBooleanTag("show_pager", $yesNoLang,$this->getParamValue("show_pager",1),$this->l('Enable Pager Navigation'),'class="select-option"','class="row"','','');

        ?>
      </ul>
    </div>
  </fieldset>
 
<br />
  <input type="submit" name="submit" value="<?php echo $this->l('Update');?>" class="button" />
  	<fieldset><legend class="lof-legend"><img src="../img/admin/comment.gif" alt="" title="" /><?php echo $this->l('Information');?></legend>    	
    	<ul>
    	     <li>+ <a target="_blank" href="http://landofcoder.com/prestashop/"><?php echo $this->l('Detail Information');?></li>
             <li>+ <a target="_blank" href="http://landofcoder.com/supports/forum.html"><?php echo $this->l('Forum support');?></a></li>
             <li>+ <a target="_blank" href="http://landofcoder.com/submit-request.html"><?php echo $this->l('Customization/Technical Support Via Email');?>.</a></li>
             <li>+ <a target="_blank" href="http://landofcoder.com/prestashop/guides/"><?php echo $this->l('UserGuide ');?></a></li>
        </ul>
        <br />
		<br/>
    <div id="fb-root"></div>
		<script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=248313105205162";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>    
    <div class="social_buttons">
      <div class="fb-like" data-href="http://www.facebook.com/LandOfCoder" data-send="false" data-width="200" data-show-faces="false"></div>
      <a href="https://twitter.com/landofcoder" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @LandOfCoder</a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
     </div>
     <br/>
     @Copyright: <a href="http://landofcoder.com">LandOfCoder.com</a>
    </fieldset>
</form>
