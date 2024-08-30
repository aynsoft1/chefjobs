let currentPage = 1;
const limit = 10;
let currentFilter = "last_30_days";

async function fetchData(page, filter) {
  const URL = base_domain+`/api/admin-report.php?page=${page}&limit=${limit}&filter=${filter}`;
  const response = await fetch(URL);
  const data = await response.json();
  return data;
}

function updateTable(data) {
  const tableBody = document.getElementById("employerTableBody");
  tableBody.innerHTML = "";
  console.log(data);
  data.forEach((row) => {
    const tableRow = document.createElement("tr");
    tableRow.innerHTML = `
      <td>${row.employer_name}</td>
      <td>${row.total_job_posted !== null ? row.total_job_posted : 0}</td>
      <td>${row.total_viewed !== null ? row.total_viewed : 0}</td>
      <td>${row.total_applications !== null ? row.total_applications : 0}</td>
      <td>${row.total_clicks !== null ? row.total_clicks : 0}</td>
    `;
    tableBody.appendChild(tableRow);
  });
}

async function loadPage(page) {
  const data = await fetchData(page, currentFilter);
  if (data.data.length === 0) {
    const tableBody = document.getElementById("employerTableBody");
    tableBody.innerHTML = "<tr><td colspan='5'>No records found</td></tr>";

    document.getElementById("prevPage").disabled = true;
    document.getElementById("nextPage").disabled = true;

    currentPage = page;
    document.getElementById("currentPage").innerText = currentPage;
  } else {
    updateTable(data.data);

    currentPage = page;
    document.getElementById("currentPage").innerText = currentPage;

    document.getElementById("prevPage").disabled = !data.isPrev;
    document.getElementById("nextPage").disabled = !data.isNext;
  }
}

function changePage(direction) {
  if (direction === "next") {
    loadPage(currentPage + 1);
  } else if (direction === "prev") {
    loadPage(currentPage - 1);
  }
}

function applyFilter() {
  const filterSelect = document.getElementById("filterSelect");
  currentFilter = filterSelect.value;
  loadPage(1); // Load the first page with the new filter
}

// Initial load
loadPage(currentPage);
