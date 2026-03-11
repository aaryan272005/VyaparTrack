const toggleBtn = document.getElementById("toggleBtn");
const sidebar = document.getElementById("DashboardSidebar");
const content = document.querySelector(".DashboardContent_container");

if (toggleBtn) {
  toggleBtn.addEventListener("click", function (e) {
    e.preventDefault();

    if (sidebar) sidebar.classList.toggle("collapsed");
    if (content) content.classList.toggle("expanded");
  });
}

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

// after add msg
document.addEventListener("DOMContentLoaded", function () {
  const msg = document.querySelector(".responseMessage");

  if (msg) {
    setTimeout(() => {
      msg.classList.add("fadeOut");
    }, 3000);
  }
});

// Img upload
const fileInput = document.getElementById("img");
const fileName = document.getElementById("fileName");

if (fileInput) {
  fileInput.addEventListener("change", function () {
    fileName.textContent = this.files[0].name;
  });
}

// FETCH SUPPLIERS WHEN PRODUCT CHANGES
document.addEventListener("change", function (e) {
  if (e.target.classList.contains("product_name")) {
    let product_id = e.target.value;
    let parent = e.target.closest(".orderProductRow");
    let supplierContainer = parent.querySelector(".supplierRows");
    let removeBtn = parent.querySelector(".removeProductRowBtn");

    // hide suppliers and remove button if reset
    if (product_id === "") {
      supplierContainer.innerHTML = "";
      removeBtn.style.display = "none";
      return;
    }

    // show remove button when product selected
    removeBtn.style.display = "inline-block";

    fetch("database/get-product-supplier.php?product_id=" + product_id)
      .then((res) => res.text())
      .then((data) => {
        supplierContainer.innerHTML = data;
      });
  }
});

// ADD NEW PRODUCT ROW
document.addEventListener("DOMContentLoaded", function () {
  const orderBtn = document.querySelector(".orderProductBtn");
  const container = document.getElementById("orderProductList");
  const template = document.getElementById("productRowTemplate");

  if (orderBtn && container && template) {
    const removeBtn = template.querySelector(".removeProductRowBtn");
    if (removeBtn) {
      removeBtn.style.display = "none";
    }

    orderBtn.addEventListener("click", function () {
      let newRow = template.cloneNode(true);

      newRow.removeAttribute("id");

      newRow.querySelector(".product_name").value = "";
      newRow.querySelector(".supplierRows").innerHTML = "";

      const btn = newRow.querySelector(".removeProductRowBtn");
      if (btn) btn.style.display = "none";

      container.prepend(newRow);
    });
  }
});

// REMOVE PRODUCT ROW
document.addEventListener("click", function (e) {
  if (e.target.closest(".removeProductRowBtn")) {
    let rows = document.querySelectorAll(".orderProductRow");

    // prevent deleting last row
    if (rows.length > 1) {
      let row = e.target.closest(".orderProductRow");
      row.remove();
    }
  }
});
