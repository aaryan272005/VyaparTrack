fetch("database/dashboard-data.php")
  .then((res) => res.json())
  .then((data) => {
    /* PIE CHART - ORDER STATUS */

    let statusData = [];

    data.status.forEach((row) => {
      statusData.push({
        name: row.stats,
        y: parseInt(row.total),
      });
    });

    Highcharts.chart("orderStatusChart", {
      chart: { type: "pie" },
      title: { text: "" },

      series: [
        {
          name: "Orders",
          data: statusData,
        },
      ],
    });

    /* BAR CHART - SUPPLIER PRODUCT COUNT */

    let supplierNames = [];
    let supplierCounts = [];

    data.supplier.forEach((row) => {
      supplierNames.push(row.supplier_name);
      supplierCounts.push(parseInt(row.total));
    });

    Highcharts.chart("supplierProductChart", {
      chart: { type: "column" },
      title: { text: "" },

      xAxis: {
        categories: supplierNames,
      },

      yAxis: {
        title: { text: "Product Count" },
      },

      series: [
        {
          name: "Suppliers",
          data: supplierCounts,
        },
      ],
    });

    /* LINE CHART - DELIVERY HISTORY */

    let days = [];
    let deliveries = [];

    data.delivery.forEach((row) => {
      days.push(row.day);
      deliveries.push(parseInt(row.total));
    });

    Highcharts.chart("deliveryHistoryChart", {
      chart: { type: "line" },

      title: { text: "" },

      xAxis: { categories: days },

      yAxis: {
        title: { text: "Product Delivered" },
      },

      series: [
        {
          name: "Product Delivered",
          data: deliveries,
        },
      ],
    });
  });
