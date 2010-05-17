<?php
/*
Plugin Name: Fahrraeder Portal
Plugin URI: http://wordpress.org/extend/plugins/fahrraeder-portal/
Description: Adds a customizeable widget which displays the latest Fahrraeder news by http://www.fahrraeder.net/
Version: 1.0
Author: Jens Janke
Author URI: http://www.fahrraeder.net/
License: GPL3
*/

function fahrradportal()
{
  $options = get_option("widget_fahrradportal");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrraeder Portal',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=fahrr%C3%A4der&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_fahrradportal($args)
{
  extract($args);
  
  $options = get_option("widget_fahrradportal");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrraeder Portal',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  fahrradportal();
  echo $after_widget;
}

function fahrradportal_control()
{
  $options = get_option("widget_fahrradportal");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrraeder Portal',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['fahrradportal-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['fahrradportal-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['fahrradportal-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['fahrradportal-CharCount']);
    update_option("widget_fahrradportal", $options);
  }
?> 
  <p>
    <label for="fahrradportal-WidgetTitle">Widget Title: </label>
    <input type="text" id="fahrradportal-WidgetTitle" name="fahrradportal-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="fahrradportal-NewsCount">Max. News: </label>
    <input type="text" id="fahrradportal-NewsCount" name="fahrradportal-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="fahrradportal-CharCount">Max. Characters: </label>
    <input type="text" id="fahrradportal-CharCount" name="fahrradportal-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="fahrradportal-Submit"  name="fahrradportal-Submit" value="1" />
  </p>
  
<?php
}

function fahrradportal_init()
{
  register_sidebar_widget(__('Fahrraeder Portal'), 'widget_fahrradportal');    
  register_widget_control('Fahrraeder Portal', 'fahrradportal_control', 300, 200);
}
add_action("plugins_loaded", "fahrradportal_init");
?>