<?php

class ImageResultsProvider 
{
  private $con;

  public function __construct($con) {
    $this->con = $con;
  }

  public function getNumResults($term)
  {
    $query = $this->con->prepare("SELECT COUNT(*) as total FROM images 
      WHERE title LIKE :term 
      OR alt LIKE :term
      AND broken = 0");
    
    $term = '%' . $term .'%';

    $query->bindParam(":term", $term);
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC);

    return $row['total'];
  }

  public function getResultsHTML($page, $resultsPerPage, $term)
  {
    $term = '%' . $term . '%';
    $fromLimit = ($page - 1) * $resultsPerPage;

    $query = $this->con->prepare("SELECT * FROM images
      WHERE title LIKE :term 
      OR alt LIKE :term
      AND broken = 0
      ORDER BY clicks DESC
      LIMIT :fromLimit, :resultsPerPage");

    $query->bindParam(":term", $term);
    $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
    $query->bindParam(":resultsPerPage", $resultsPerPage, PDO::PARAM_INT);
    $query->execute();

    $html = '';

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $src = $row['src'];
      $alt = $row['alt'];
      $title = $row['title'];

      // Set displayText value
      if ($title) {
        $displayText = $title;
      } else if ($alt) {
        $displayText = $alt;
      } else {
        $displayText = $src;
      }

      $html .= "
        <a href='$src' class='results__item--image'>
          <img src='$src' alt='$alt'>
        </a>
      ";
    }

    return $html;
  }
}

