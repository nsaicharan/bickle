import UpdateClicksCount from "./modules/UpdateClicksCount";
import Masonry from "masonry-layout";

window.addEventListener("load", () => {
  new UpdateClicksCount();

  // Display image results in masonry layout
  new Masonry(".results__images", {
    columnWidth: 241,
    itemSelector: ".results__item--image",
    gutter: 10
    // initLayout: false
  });
});
