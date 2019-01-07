<?php
  require "config.php";
  require "includes/WebResultsProvider.php";

  // Check if search term (q) exists
  if (!isset($_GET['q']) || empty($_GET['q'])) {
    header("Location: /");
    exit;
  } else {
    $term = $_GET['q'];
    $self = $_SERVER['PHP_SELF'];
    $type = "web";

    // Check if type is images
    if (isset($_GET['type']) && $_GET['type'] === "images") {
      $type = "images";
    } 
  }
?>

<?php require "includes/header.php" ?>

  <header class="search-header">
    <div class="container container--narrow">
      <div class="text-center">
        <a href="/" class="search-header__title">Bickle</a>
      </div >

      <form role="search" action='<?= $self ?>' class="search-header__form">
        <input type="text" class="search-header__input" name="q" value="<?= $term; ?>">
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

  <section class="results">
    <div class="container container--narrow">
      <div class="results__count">
        <?php
          $resultsProvider = new WebResultsProvider($con);
          $resultsCount = $resultsProvider->getNumResults($term);
          echo "$resultsCount results found.";
        ?>
      </div>
      <!-- /.results__count -->

      <div class="results__data">
        <?php 
          $resultsArray = $resultsProvider->getResults(1, 20, $term);
          
          foreach ($resultsArray as $result) :
        ?>
          <div class="results__item">
            <a href="<?= $result['url'] ?>" class="results__link">
              <h2 class="results__title">
                <?= $result['title'] ?>
              </h2>

              <cite class="results__url"><?= $result['url'] ?></cite>
            </a>

            <p class="results__description"><?= $result['description'] ?></p>
          </div>
          <!-- ./results__item -->
        <?php endforeach; ?>
      </div>
      <!-- ./results__data -->
    </div>
    <!-- /.container -->
  </section>

<?php require "includes/footer.php" ?>
