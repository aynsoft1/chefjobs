let currentJobPage = 1;
const joblimit = 10;
let currentJobFilter = "last_30_days";

async function fetchJobPeformanceData(page, filter) {
  const URL = base_domain+`/api/admin-report.php?data_type=job&page=${page}&limit=${joblimit}&filter=${filter}`;
  const response = await fetch(URL);
  const data = await response.json();
  return data;
}

function updateJobPerformanceTable(data) {
  const tableBody = document.getElementById("employerJobTableBody");
  tableBody.innerHTML = "";
  console.log(data);
  data.forEach((row) => {
    const tableRow = document.createElement("tr");
    tableRow.innerHTML = `
      <td>${row.job_title}</td>
      <td>${row.employer_full_name !== null ? row.employer_full_name : 0}</td>
      <td>${row.total_viewed !== null ? row.total_viewed : 0}</td>
      <td>${row.total_applications !== null ? row.total_applications : 0}</td>
      <td>${row.total_clicks !== null ? row.total_clicks : 0}</td>
    `;
    tableBody.appendChild(tableRow);
  });
}

async function loadJobPerformancePage(page) {
  const data = await fetchJobPeformanceData(page, currentJobFilter);
  if (data.data.length === 0) {
    const tableBody = document.getElementById("employerJobTableBody");
    tableBody.innerHTML = "<tr><td colspan='5'>No records found</td></tr>";

    document.getElementById("prevJobPage").disabled = true;
    document.getElementById("nextJobPage").disabled = true;

    currentJobPage = page;
    document.getElementById("currentJobPage").innerText = currentJobPage;
  } else {
    updateJobPerformanceTable(data.data);

    currentJobPage = page;
    document.getElementById("currentJobPage").innerText = currentJobPage;

    document.getElementById("prevJobPage").disabled = !data.isPrev;
    document.getElementById("nextJobPage").disabled = !data.isNext;
  }
}

function changeJobPerformancePage(direction) {
  if (direction === "next") {
    loadJobPerformancePage(currentJobPage + 1);
  } else if (direction === "prev") {
    loadJobPerformancePage(currentJobPage - 1);
  }
}

function applyJobPerformanceFilter() {
  const filterSelect = document.getElementById("filterJobSelect");
  currentJobFilter = filterSelect.value;
  loadJobPerformancePage(1); // Load the first page with the new filter
}

// Initial load
loadJobPerformancePage(currentJobPage);
