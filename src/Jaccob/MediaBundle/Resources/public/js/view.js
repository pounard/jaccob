/*global Packery, console */
/*jslint browser: true, for: true, white: true */

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

  var nodes, i, imgList = [], container, pack = [];

  nodes = document.querySelectorAll(".pack img");
  if (!nodes.length) {
    return;
  }

  // Convert img to an array.
  for (i = 0; i < nodes.length; i += 1) {
    // We don't want empty or erroneous images to reserve space, just use the
    // 1px trick so that the browser may still load it anyway. Please note that
    // when loading images from cache, the browser might be faster than us.
    if (!nodes[i].complete) {
      nodes[i].setAttribute("height", 1);
      imgList.push(nodes[i]);
    }
  }

  function doCheck() {
    var changed = false;

    imgList.forEach(function (img, index) {
      // This means the image has been loaded, if the browser had set a natural
      // width, this also means the image was loaded from cache, and so will
      // support bugguy Chrome browser, etc...
      if (img.complete) {

        if (!img.naturalWidth) {
          // This image failed, I guess.
          imgList.splice(index, 1);
          return;
        }

        // Removing height and width attributes will allow CSS to correctly
        // display slightly reduced versions of images, allowing them to pack
        // correctly in the Bootstrap grid, but we cannot really do that cause
        // Packery needs to be able to fill the gaps whenever an image is not
        // fully loaded.
        img.removeAttribute("height");
        img.removeAttribute("width");

        // Thanks https://stackoverflow.com/a/3199627
        imgList.splice(index, 1);
        changed = true;
      }
    });

    if (changed) {
      pack.forEach(function (item) {
        item.layout();
      });
    }

    // This is some sort of active wait, don't like it, but at least it does
    // work on every browser. Why 100 ? Why not. We don't do anything too
    // heavy in there, it's good enough.
    if (imgList.length) {
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
    // isInitLayout: false,
    gutter: 0
  }));

  doCheck();

}(document, Packery, console));