<?php
/**
 * Simple [table] Code
 * Copyright 2019 MINI² e.V.
 * License: GNU General Public License v3.0
 *
 * Replaces [table] codes with HTML tables, uses new Lines to separate rows and Pipes (|) to separate cells
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
  die("Direct initialization of this file is not allowed.");
}

function simpletablecode_info()
{
  return array(
      "name"          => "Simple MyBB [table] Code",
      "description"   => "Replaces [table] codes with HTML tables, uses new Lines to separate rows and Pipes (|) to separate cells",
      "website"       => "https://www.mini2.info",
      "author"        => "A-Dalton (MINI²)",
      "authorsite"    => "https://github.com/A-Dalton",
      "version"       => "0.1",
      "guid"          => "",
      "codename"      => str_replace('.php', '', basename(__FILE__)),
      "compatibility" => "*"
  );
}

// Execute the 'tableCode_replacer' method whenever a message is parsed
$plugins->add_hook("parse_message", "tableCode_replacer");

function tableCode_replacer($message)
{

  // $message = "Siehe hier:\n\n[table]H1|H2|[b]H3[/b]\nR1H1|R1H2\nR2H1||R1H3[/table] \n\n\nENDE";

  $tableMatchRegex = "/\[table\].*?\[\/table\]|\[table=.*\].*?\[\/table\]/is";
  $matchesCount = preg_match_all($tableMatchRegex, $message, $matches);

 if ($matchesCount)
 {
   // Split contains everything outside the match (two empty strings if the entire string matches)
   $split = preg_split($tableMatchRegex, $message);

   // Used to count and re-combine the non-table parts of the message
   $iSp = 0;

  $newMessage = $split[$iSp++];
  foreach ($matches[0] as $match)
  {
    $newMessage .= '<table class="tborder">';
    $table = preg_replace("/\[table\](.*)\[\/table\]/is", "$1", $match);

    // Jeder Eintrag in $tblRows ergibt eine '<tr>'
    $iRow = 0;
    $tblRows = explode("\n", $table);
    foreach($tblRows as $tblRow)
    {
      if ($iRow == 0) {
        $newMessage .= '<thead><tr class="thead">';
      }
      elseif ($iRow == 1) {
        $newMessage .= '<tbody><tr>';
      }
      else {
        $newMessage .= "<tr>";
      }

      foreach(explode("|", $tblRow) as $tblCell)
      {
        if ($iRow == 0){
          $newMessage .= '<td style="padding: 5px">'.$tblCell.'</td>';
        }
        elseif ($iRow%2==1){
          $newMessage .= '<td class="trow2">'.$tblCell.'</td>';
        }
        else{
          $newMessage .= '<td class="trow1">'.$tblCell.'</td>';
        }
      }

      if ($iRow == 0) {
        $newMessage .= "</tr></thead>";
      }
      elseif (($iRow + 1) == count($tblRows)) {
        $newMessage .= "</tr></tbody>";
      }
      else {
        $newMessage .= "</tr>";
      }

      $iRow++;
    }

    $newMessage .= "</table>".$split[$iSp++];
  }

  return $newMessage;
 }
 else
 {
   return $message;
 }
}
