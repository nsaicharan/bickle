class UpdateClicksCount {
  constructor() {
    this.results = document.querySelectorAll(".js-result");
    this.events();
  }

  events() {
    this.results.forEach(result =>
      result.addEventListener("click", this.handleClick)
    );
  }

  handleClick(e) {
    e.preventDefault();

    const result = e.target.parentNode;

    const data = new FormData();
    data.append("id", result.dataset.id);
    data.append("key", "incrementSiteClicks");

    fetch("process.php", {
      method: "post",
      body: data
    }).then(() => (window.location.href = result.getAttribute("href")));
  }
}

export default UpdateClicksCount;
