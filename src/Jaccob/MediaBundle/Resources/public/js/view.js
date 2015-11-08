// God, I hate JavaScript, and most of all, I hate Chrome. Fuck you Google.
(function (document, Packery, console) {
  "use strict";

  // In order to allow older browsers to graceful downgrade we must avoid them
  // throwing bad JS errors, just degrade silently in that case and leave the
  // browser misplace the blocks, due the Bootstrap grid there will be a lot of
  // empty white space, but at least all images will remain visible for the end
  // user.
  if (!document.querySelectorAll || !Packery) {
    return;
  }

  var nodes, i, img = [], container, pack = [];

  nodes = document.querySelectorAll(".pack img");
  if (!nodes.length) {
    return;
  }

  // Convert img to an array.
  for (i = 0; i < nodes.length; i++) {
    img.push(nodes[i]);

    // We don't want empty or erroneous images to reserve space, just use
    // the 1px trick so that the browser may still load it anyway.
    img[i].setAttribute("height", 1);
    img[i].setAttribute("width", 1);
  }

  function doCheck() {
    var key, removed = false;

    for (key in img) {
      // This means the image has been loaded, if the browser had set a natural
      // width, this also means the image was loaded from cache, and so will
      // support bugguy Chrome browser, etc...
      if (img[key].complete) {

        if (!img[key].naturalWidth) {
          // This image failed, I guess.
          img.splice(key, 1);
          continue;
        }

        // Removing height and width attributes will allow CSS to correctly
        // display slightly reduced versions of images, allowing them to pack
        // correctly in the Bootstrap grid, but we cannot really do that cause
        // Packery needs to be able to fill the gaps whenever an image is not
        // fully loaded.
        img[key].removeAttribute("height");
        img[key].removeAttribute("width");

        // Thanks https://stackoverflow.com/a/3199627
        img.splice(key, 1);
        removed = true;
      }
    }

    if (removed) {
      for (key in pack) {
        pack[key].layout();
      }
    }

    // This is some sort of active wait, don't like it, but at least it does
    // work on every browser. Why 100 ? Why not. We don't do anything too
    // heavy in there, it's good enough.
    if (img.length) {
      setTimeout(doCheck, 100);
    } else {
      if (console) {
        console.log("Pack is done");
      }
    }
  }

  // Initialize packery.
  container = document.querySelector(".pack");
  pack.push(new Packery(container, {
    itemSelector: ".media-thumbnail",
    transitionDuration: 0,
    isInitLayout: false,
    gutter: 0
  }));

  doCheck();

}(document, Packery, console));