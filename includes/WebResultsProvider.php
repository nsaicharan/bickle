<?php

class WebResultsProvider 
{
  private $con;

  public function __construct($con) {
    $this->con = $con;
  }

  public function getNumResults($term)
  {
    $query = $this->con->prepare("SELECT COUNT(*) as total FROM sites 
      WHERE title LIKE :term
      OR url LIKE :term
      OR keywords LIKE :term
      OR description LIKE :term");
    
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

    $query = $this->con->prepare("SELECT * FROM sites
      WHERE title LIKE :term
      OR url LIKE :term
      OR keywords LIKE :term
      OR description LIKE :term
      ORDER BY clicks DESC
      LIMIT :fromLimit, :resultsPerPage");

    $query->bindParam(":term", $term);
    $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
    $query->bindParam(":resultsPerPage", $resultsPerPage, PDO::PARAM_INT);
    $query->execute();

    $results = [];

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
      // Limit the length of title and description
      $row['title'] = $this->trimField($row['title'], 70);
      $row['description'] = $this->trimField($row['description'], 200);

      // Push row data into results array
      $results[] = $row;
    }

    return $results;
  }

  private function trimField($string, $characterLimit)
  {
    $dots = strlen($string) > $characterLimit ? '...' : '';
    
    return substr($string, 0, $characterLimit) . $dots;
  }
}

