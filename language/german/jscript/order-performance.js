let currentOrderPage = 1;
const limitOrder = 10;
let currentOrderFilter = "recruiter";

async function fetchOrderData(page, filter) {
  const URL = base_domain+`/api/admin-report.php?data_type=orders&page=${page}&limit=${limitOrder}&filter=${filter}`;
  const response = await fetch(URL);
  const data = await response.json();
  return data;
}

function updateOrderTable(data) {
  const tableBody = document.getElementById("orderTableBody");
  tableBody.innerHTML = "";
  console.log(data);
  data.forEach((row) => {
    const tableRow = document.createElement("tr");
    tableRow.innerHTML = `
      <td>${row.orders_id}</td>
      <td>${row.name}</td>
      <td>${row.type}</td>
      <td>${row.status}</td>
      <td>${row.total !== null ? row.total : 0}</td>
    `;
    tableBody.appendChild(tableRow);
  });
}

async function loadOrderPage(page) {
  const data = await fetchOrderData(page, currentOrderFilter);
  if (data.data.length === 0) {
    const tableBody = document.getElementById("orderTableBody");
    tableBody.innerHTML = "<tr><td colspan='5'>No records found</td></tr>";

    document.getElementById("prevOrderPage").disabled = true;
    document.getElementById("nextOrderPage").disabled = true;

    currentOrderPage = page;
    document.getElementById("currentOrderPage").innerText = currentOrderPage;
  } else {
    updateOrderTable(data.data);

    currentOrderPage = page;
    document.getElementById("currentOrderPage").innerText = currentOrderPage;

    document.getElementById("prevOrderPage").disabled = !data.isPrev;
    document.getElementById("nextOrderPage").disabled = !data.isNext;
  }
}

function changeOrderPage(direction) {
  if (direction === "next") {
    loadOrderPage(currentOrderPage + 1);
  } else if (direction === "prev") {
    loadOrderPage(currentOrderPage - 1);
  }
}

function applyOrderFilter() {
  const filterSelect = document.getElementById("filterOrderSelect");
  currentOrderFilter = filterSelect.value;
  loadOrderPage(1); // Load the first page with the new filter
}

// Initial load
loadOrderPage(currentOrderPage);
