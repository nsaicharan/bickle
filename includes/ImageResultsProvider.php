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

  public function getResults($page, $resultsPerPage, $term)
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

    $results = [];

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {

      // Add 'displayText' to row data
      if ($row['title']) {
        $row['displayText'] = $row['title'];
      } else if ($row['alt']) {
        $row['displayText'] = $row['alt'];
      } else {
        $row['displayText'] = $row['src'];
      }

      // Push row data into results array
      $results[] = $row;
    }

    return $results;
  }
}

