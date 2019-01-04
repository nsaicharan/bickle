<?php require "includes/header.php" ?>

  <section class="front-page-wrapper">
    <h1 class="site-title">Bickle</h1>

    <form role="search" action="search.php" method="GET" class="main-form">
      <label for="q" class="sr-only">What are you looking for?</label>
      <input type="text" class="main-form__input" id="q" name="q" autofocus placeholder="What are you looking for?">

      <button class="main-form__btn">Begin Search</button>
    </form>
  </section>

<?php require "includes/footer.php" ?>
