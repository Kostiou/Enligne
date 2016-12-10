<?php

require_once('libs/Smarty.class.php');

$smarty = new Smarty();   //classe objet smarty

$smarty->setTemplateDir('templates/');      //repertoire où se trouve nos templates
$smarty->setCompileDir('templates_c/');     //sorte de fichier cache où est complilé une page déja visitée
//$smarty->setConfigDir('/web/www.example.com/guestbook/configs/');
//$smarty->setCacheDir('/web/www.example.com/guestbook/cache/');

$name = "Thomas";

$smarty->assign('name',$name);              //Fonction Smarty = variable 

$smarty->debugging = true;                  //console de débug

$smarty->display('smarty.tpl');             //Fonction display va chercher fichier tpl et la charge

?>
