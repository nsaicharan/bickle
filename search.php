<?php
  session_start();
  $_SESSION['search'] = true; // Will be used in process.php while updating clicks count 

  require "config.php";
  require "includes/WebResultsProvider.php";
  require "includes/ImageResultsProvider.php";

  $self = str_replace('.php', '', htmlspecialchars($_SERVER['PHP_SELF']));

  // Check if search term (q) exists
  if (!isset($_GET['q']) || empty($_GET['q'])) {
    header("Location: $self/.."); // Go back
    exit;
  } else {
    $term = $_GET['q'];

    // Set type, page, results per page
    $type = isset($_GET['type']) && $_GET['type'] == 'images' ? 'images' : 'web';
    $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1;
    $resultsPerPage = isset($_GET['type']) && $_GET['type'] == 'images' ? 20 : 10;
  }
?>

<?php require "includes/header.php" ?>

  <header class="search-header">
    <div class="container container--narrow">
      <div class="text-center">
        <a href='<?= "$self/.." ?>' class="search-header__title">Bickle</a>
      </div >

      <form role="search" action='<?= $self ?>' class="search-header__form">
        <input type="text" class="search-header__input" name="q" value="<?= $term; ?>">
        <input type="hidden" name="type" value="<?= $type ?>">
        <button class="search-header__submit-btn">
          <img src="assets/img/search.svg" alt="search icon">
        </button>
      </form>

      <a 
        href='<?= "$self?q=$term&type=web" ?>' 
        class="search-header__tab <?= ($type == 'web') ? 'search-header__tab--active' : '' ?>"
      >
        Web
      </a>

      <a 
        href='<?= "$self?q=$term&type=images" ?>' 
        class="search-header__tab <?= ($type == 'images') ? 'search-header__tab--active' : '' ?>"
      >
        Images
      </a>
    </div>
    <!-- /.container -->
  </header>


  <section class="results container container--narrow">
    <div class="results__count">
      <?php
        if ($type == 'web') {
          $resultsProvider = new WebResultsProvider($con);
        } else {
          $resultsProvider = new ImageResultsProvider($con);
        }

        $resultsCount = $resultsProvider->getNumResults($term);

        echo "$resultsCount results found.";
      ?>
    </div>
    <!-- /.results__count -->

    <?php 
      $resultsHTML= $resultsProvider->getResultsHTML($page, $resultsPerPage, $term);

      if ($type == 'web') :
    ?>
    
      <div class="results__web">
        <?= $resultsHTML; ?>
      </div>

    <?php else: ?>

      <div class="results__images">
        <?= $resultsHTML; ?>
      </div>

    <?php endif; ?>
  </section>


  <div class="container container--narrow">
    <nav class="pagination" aria-label="Page Navigation">

      <!-- Previous Button -->
      <?php if ($page - 1 > 0) : ?>
        <a 
          href='<?= "$self?q=$term&type=$type&page=" . ($page - 1) ?>'
          class="pagination__link pagination__link--before"
          title="Previous Page"
        >
          <img src="assets/img/left-chevron.svg" alt="" aria-hidden="true">

          <span class="sr-only">Previous Page.</span>
        </a>
      <?php endif; ?>
      
      <!-- Pagination Numbers -->
      <?php 
        $totalPages = ceil($resultsCount / $resultsPerPage);
        $pagesToShow = 7;
        $paginationStartingPoint = $page - floor($pagesToShow / 2);

        if ($paginationStartingPoint < 1) {
          $paginationStartingPoint = 1;
        }

        for ($i = 0; $i < $pagesToShow; $i++) :
          $paginationNumber = $paginationStartingPoint + $i;

          if ($paginationNumber <= $totalPages) :
      ?>
        <a  
          href='<?= "$self?q=$term&type=$type&page=$paginationNumber" ?>'
          class="pagination__link <?= ($page == $paginationNumber) ? 'pagination__link--active' : '' ?>"
        >
          <?= $paginationNumber ?>
        </a>
      <?php endif; endfor; ?>

      <!-- Next Button -->
      <?php if ($page + 1 < $totalPages) : ?>
        <a 
          href='<?= "$self?q=$term&type=$type&page=" . ($page + 1) ?>'
          class="pagination__link pagination__link--after"
          title="Next Page"
        >
          <img src="assets/img/left-chevron.svg" alt="" aria-hidden="true">

          <span class="sr-only">Next Page.</span>
        </a>
      <?php endif; ?>

    </nav>
  </div>
  <!-- ./container -->


<?php require "includes/footer.php" ?>
