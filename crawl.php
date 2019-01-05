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

function getDetails($url)
{
  $parser = new DOmDocumentParser($url);
  
  // Get Title
  $titleArray = $parser->getTitleTags();

  if (sizeof($titleArray) === 0 || $titleArray->item(0) === NULL) {
    return;
  }

  $title = $titleArray->item(0)->nodeValue;
  $title = str_replace('\n', '', $title);

  if ($title === '') {
    return;
  }

  // Get Description, Keywords
  $description = '';
  $keywords = '';
  $metasArray = $parser->getMetatags();

  foreach ($metasArray as $meta) {
    if ($meta->getAttribute('name') == 'description') {
      $description = $meta->getAttribute('content');
    }

    if ($meta->getAttribute('name') == 'keywords') {
      $keywords = $meta->getAttribute('content');
    }
  }

  $description = str_replace('\n', '', $description);
  $keywords = str_replace('\n', '', $keywords);

  echo "URL: $url, TITLE: $title, Description: $description, Keywords: $keywords <br>";
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

      getDetails($href);
    } else {
      return;
    }
  }

  array_shift($crawling);

  foreach ($crawling as $site) {
    followLinks($site);
  }

}

$startUrl = "https://bbc.com";
followLinks($startUrl);

?>