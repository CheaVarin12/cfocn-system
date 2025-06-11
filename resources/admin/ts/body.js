feather.replace();
Alpine.start();

$(document).ready(function () {
  // Scroll To Active
  const menu_active = $(
    ".sidebar .sidebar-wrapper .menu-list .menu-item.active"
  );
  const menu_list = menu_active.parents(".menu-list")[0];
  menu_list?.scrollTo({
    top: menu_active[0].offsetTop - menu_list.clientHeight / 2,
    left: 0,
    behavior: "smooth",
  });
});

require("s-event.js");
require("s-mask.js");

window.reloadUrl = function (url) {
  window.location.href = url;
};
