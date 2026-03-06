const toggleBtn = document.getElementById("toggleBtn");
const sidebar = document.getElementById("DashboardSidebar");
const content = document.querySelector(".DashboardContent_container");

/* SIDEBAR COLLAPSE */

toggleBtn.addEventListener("click", function (e) {
  e.preventDefault();

  sidebar.classList.toggle("collapsed");
  content.classList.toggle("expanded");
});

/* SUBMENU */

document.querySelectorAll(".has-submenu > a").forEach((menu) => {
  menu.addEventListener("click", function (e) {
    e.preventDefault();

    const parent = this.parentElement;

    document.querySelectorAll(".liMenu.open").forEach((item) => {
      if (item !== parent) {
        item.classList.remove("open");
      }
    });

    parent.classList.toggle("open");
  });
});
