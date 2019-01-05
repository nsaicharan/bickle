<?php

require "includes/DomDocumentParser.php";

$alreadyCrawled = array();
$crawling = array();

function createLink($src, $url)
{
  $scheme = parse_url($url)['scheme']; // http or https
  $host = parse_url($url)['host']; // www.example.com

  /**
   * Converting relative links into absolute links
   * 
   * Patterns converted:
   *   //example.com
   *   /about/team.html
   *   ./about/team.html
   *   ../about/team.html
   *   about/team.html
   */
  if (substr($src, 0, 2) == '//') { 
    $src = $scheme . ':' . $src;
  } else if (substr($src, 0, 1) == '/') {
    $src = $scheme . '://' . $host . $src;
  } else if (substr($src, 0, 2) == './') {
    $src = $scheme . '://' . $host . dirname(parse_url($url)['path']) . substr($src, 1);
  } else if (substr($src, 0, 3) == '../') {
    $src = $scheme . '://' . $host . '/' . $src;
  }  else if (substr($src, 0, 4) != 'http' && substr($src, 0, 5) != "https") {
    $src = $scheme . '://' . $host . '/' . $src;
  }

  return $src;
}

function followLinks($url) 
{
  global $alreadyCrawled;
  global $crawling;

  $parser = new DomDocumentParser($url);
  $linkList = $parser->getLinks();

  foreach ($linkList as $link) {
    $href = $link->getAttribute("href");

    // Skip links starting with "#" or "javascript:"
    if (strpos($href, "#") !== false || substr($href, 0, 11) == "javascript:") {
      continue;
    } 

    $href = createLink($href, $url);

    if (!in_array($href, $alreadyCrawled)) {
      $alreadyCrawled[] = $href;
      $crawling[] = $href;
    }

    echo $href . "<br>";
  }

  array_shift($crawling);

  foreach ($crawling as $site) {
    followLinks($site);
  }

}

$startUrl = "https://projects.saicharan.com/parusworld";
followLinks($startUrl);
