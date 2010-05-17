<?php
/*
Plugin Name: Fahrraeder Portal
Plugin URI: http://wordpress.org/extend/plugins/fahrraeder-portal/
Description: Adds a customizeable widget which displays the latest Fahrraeder news by http://www.fahrraeder.net/
Version: 1.1
Author: Simone Buhlmann
Author URI: http://www.fahrraeder.net/
License: GPL3
*/

function fahrradnew()
{
  $options = get_option("widget_fahrradnew");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrad Information',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=fahrrad&cf=all&output=rss'); 
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

function widget_fahrradnew($args)
{
  extract($args);
  
  $options = get_option("widget_fahrradnew");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrad Information',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  fahrradnew();
  echo $after_widget;
}

function fahrradnew_control()
{
  $options = get_option("widget_fahrradnew");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrad Information',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['fahrradnew-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['fahrradnew-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['fahrradnew-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['fahrradnew-CharCount']);
    update_option("widget_fahrradnew", $options);
  }
?> 
  <p>
    <label for="fahrradnew-WidgetTitle">Widget Title: </label>
    <input type="text" id="fahrradnew-WidgetTitle" name="fahrradnew-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="fahrradnew-NewsCount">Max. News: </label>
    <input type="text" id="fahrradnew-NewsCount" name="fahrradnew-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="fahrradnew-CharCount">Max. Characters: </label>
    <input type="text" id="fahrradnew-CharCount" name="fahrradnew-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="fahrradnew-Submit"  name="fahrradnew-Submit" value="1" />
  </p>
  
<?php
}

function fahrradnew_init()
{
  register_sidebar_widget(__('Fahrrad Information'), 'widget_fahrradnew');    
  register_widget_control('Fahrrad Information', 'fahrradnew_control', 300, 200);
}
add_action("plugins_loaded", "fahrradnew_init");
?>