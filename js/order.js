document.querySelectorAll(".updateOrderBtn").forEach((btn) => {
  btn.addEventListener("click", function () {
    let id = this.dataset.id;
    let product = this.dataset.product;
    let ordered = this.dataset.ordered;
    let received = this.dataset.received;
    let supplier = this.dataset.supplier;

    Swal.fire({
      title: "Update Purchase Order",

      html: `
<table style="width:100%;text-align:left">

<tr>
<td><b>Product</b></td>
<td>${product}</td>
</tr>

<tr>
<td><b>Qty Ordered</b></td>
<td>${ordered}</td>
</tr>

<tr>
<td><b>Qty Received</b></td>
<td>${received}</td>
</tr>

<tr>
<td><b>Qty Delivered</b></td>
<td><input type="number" id="qtyDelivered" class="swal2-input"></td>
</tr>

<tr>
<td><b>Supplier</b></td>
<td>${supplier}</td>
</tr>

<tr>
<td><b>Status</b></td>
<td>
<select id="statusSelect" class="swal2-input">
<option value="pending">pending</option>
<option value="incomplete">incomplete</option>
<option value="complete">complete</option>
</select>
</td>
</tr>

</table>
`,

      confirmButtonText: "Update",

      preConfirm: () => {
        let qty = document.getElementById("qtyDelivered").value;
        let status = document.getElementById("statusSelect").value;

        return fetch("database/update-order.php", {
          method: "POST",

          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },

          body:`order_id=${id}&quantity_delivered=${qty}&status=${status}`,
        }).then((res) => res.json());
      },
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire("Updated!", "Order updated successfully", "success");

        setTimeout(() => location.reload(), 1000);
      }
    });
  });
});

document.querySelectorAll(".viewDeliveryBtn").forEach((btn) => {
  btn.addEventListener("click", function () {
    let id = this.dataset.id;

    fetch(`database/get-delivery-history.php?order_id=${id}`)
      .then((res) => res.json())
      .then((data) => {
        let rows = "";

        data.forEach((item, i) => {
          rows += `
<tr>
<td>${i + 1}</td>
<td>${item.date_received}</td>
<td>${item.quantity_received}</td>
</tr>
`;
        });

        Swal.fire({
          title: "Delivery Histories",

          html: `
<table style="width:100%">
<tr>
<th>#</th>
<th>Date Received</th>
<th>Quantity Received</th>
</tr>
${rows}
</table>
`,
        });
      });
  });
});
