<?php
  // Check if query exists
  if (!isset($_GET['q']) || empty($_GET['q'])) {
    header("Location: /");
    exit;
  } else {
    $query = $_GET['q'];
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
        <input type="text" class="search-header__input" name="q" value="<?= $query; ?>">
        <button class="search-header__submit-btn">
          <img src="assets/img/search.svg" alt="search icon">
        </button>
      </form>

      <a 
        href='<?= "$self?q=$query&type=web" ?>' 
        class="search-header__tab <?= ($type == 'web') ? 'search-header__tab--active' : '' ?>"
      >
        Web
      </a>

      <a 
        href='<?= "$self?q=$query&type=images" ?>' 
        class="search-header__tab <?= ($type == 'images') ? 'search-header__tab--active' : '' ?>"
      >
        Images
      </a>
    </div>
    <!-- /.container -->
  </header>

<?php require "includes/footer.php" ?>
