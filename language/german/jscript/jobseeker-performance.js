let currentJobseekerPage = 1;
const limitJobseeker = 10;
let currentJobseekerFilter = "last_30_days";

async function fetchJobseekerData(page, filter) {
  const URL = base_domain+`/api/admin-report.php?data_type=jobseeker&page=${page}&limit=${limitJobseeker}&filter=${filter}`;
  const response = await fetch(URL);
  const data = await response.json();
  return data;
}

function updateJobseekerTable(data) {
  const tableBody = document.getElementById("jobseekerTableBody");
  tableBody.innerHTML = "";
  console.log(data);
  data.forEach((row) => {
    const tableRow = document.createElement("tr");
    tableRow.innerHTML = `
      <td>${row.jobseeker_name}</td>
      <td>${row.total_resume_posted !== null ? row.total_resume_posted : 0}</td>
      <td>${row.total_viewed !== null ? row.total_viewed : 0}</td>
      <td>${row.total_clicks !== null ? row.total_clicks : 0}</td>
    `;
    tableBody.appendChild(tableRow);
  });
}

async function loadJobseekerPage(page) {
  const data = await fetchJobseekerData(page, currentJobseekerFilter);
  if (data.data.length === 0) {
    const tableBody = document.getElementById("jobseekerTableBody");
    tableBody.innerHTML = "<tr><td colspan='5'>No records found</td></tr>";

    document.getElementById("prevJobseekerPage").disabled = true;
    document.getElementById("nextJobseekerPage").disabled = true;

    currentJobseekerPage = page;
    document.getElementById("currentJobseekerPage").innerText = currentJobseekerPage;
  } else {
    updateJobseekerTable(data.data);

    currentJobseekerPage = page;
    document.getElementById("currentJobseekerPage").innerText = currentJobseekerPage;

    document.getElementById("prevJobseekerPage").disabled = !data.isPrev;
    document.getElementById("nextJobseekerPage").disabled = !data.isNext;
  }
}

function changeJobseekerPage(direction) {
  if (direction === "next") {
    loadJobseekerPage(currentJobseekerPage + 1);
  } else if (direction === "prev") {
    loadJobseekerPage(currentJobseekerPage - 1);
  }
}

function applyJobseekerFilter() {
  const filterSelect = document.getElementById("filterJobseekerSelect");
  currentJobseekerFilter = filterSelect.value;
  loadJobseekerPage(1); // Load the first page with the new filter
}

// Initial load
loadJobseekerPage(currentJobseekerPage);
