<?php

function generatebutton($act, $class, $click, $rank, $text)
{
  $disable = ($rank == 1) ? '': 'disabled';

  switch ($act) {
    case 'normal':
      if($disable == '') $button = "<button type='button' class='$class' onclick='$click'>$text</button>";
      else $button = "<button type='button' class='$class' $disable>$text</button>";
      break;

    case 'link_blank_href':
      if($disable == '') $button = "<a class='$class' href='$click' target='_blank'>$text</a>";
      else $button = "<a class='$class' $disable>$text</a>";
      break;

    case 'link_blank_click':
      if($disable == '') $button = "<a class='$class' onclick='$click' target='_blank'>$text</a>";
      else $button = "<a class='$class' target='_blank' $disable>$text</a>";
      break;

    case 'link_self_href':
      if($disable == '') $button = "<a class='$class' href='$click' target='_self'>$text</a>";
      else $button = "<a class='$class' $disable>$text</a>";
      break;

    case 'link_self_click':
      if($disable == '') $button = "<a class='$class' onclick='$click' target='_self'>$text</a>";
      else $button = "<a class='$class' $disable>$text</a>";
      break;

    case 'datasort':
      if($disable == '') $button = "<button type='button' class='$class' data-action='$click'>$text</button>";
      else $button = "<button type='button' class='$class' $disable>$text</button>";
      break;

    default:
      $button = '';
      break;
  }
  return $button;
}

?>
