let currentInterval = "monthly"; // Default interval
const USER_CHART_API = '../api/chart-data.php?type=users';
const SALES_CHART_API = '../api/chart-data.php?type=sales';

function fetchData(interval) {
  fetch(USER_CHART_API + "&interval=" + interval)
    .then((response) => response.json())
    .then((data) => {
      updateChart(data);
    })
    .catch((error) => console.error("Error fetching data:", error));
}

function changeInterval(interval) {
  currentInterval = interval;
  fetchData(interval);
}

function updateChart(data) {
  const labels = data.jobseekers.map((entry) => {
    if (currentInterval === "monthly") {
      return entry.month;
    } else if (currentInterval === "weekly") {
      return entry.week;
    } else if (currentInterval === "daily") {
      return entry.day;
    }
  });

  let jobseekerData;
  let recruiterData;
  if (currentInterval === "weekly") {
    jobseekerData = data.jobseekers.map((entry) => entry.jobseeker_count);
    recruiterData = data.recruiters.map((entry) => entry.recruiter_count);
  } else {
    jobseekerData = data.jobseekers.map((entry) => entry.count);
    recruiterData = data.recruiters.map((entry) => entry.count);
  }

  const ctx = document.getElementById("chart_1").getContext("2d");

  if (window.myChart instanceof Chart) {
    window.myChart.destroy();
  }

  window.myChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Jobseekers",
          data: jobseekerData,
          backgroundColor: "#007bff",
          borderColor: "#007bff",
          pointBorderColor: "#007bff",
          pointBackgroundColor: "#007bff",
        },
        {
          label: "Recruiters",
          data: recruiterData,
          backgroundColor: "#ced4da",
          borderColor: "#ced4da",
          pointBorderColor: "#ced4da",
          pointBackgroundColor: "#ced4da",
        },
      ],
    },
    options: {
      maintainAspectRatio: true,
      scales: {
        x: {
          grid: {
            display: false,
          },
          ticks: {
            display: true,
            maxRotation: 0,
          },
        },
        y: {
          grid: {
            display: false,
          },
          ticks: {
            display: true,
            precision: 0,
          },
        },
      },
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        title: {
          display: false,
          text: "Members",
          font: {
            size: 16,
          },
        },
        tooltip: {
          mode: "index",
          intersect: false, // ensures the tooltip appears when hovering anywhere on the x-axis not just over the points
          backgroundColor: "#ffffff",
          titleColor: "#543453",
          callbacks: {
            labelTextColor: function () {
              return "#543453";
            },
          },
        },
      },
    },
  });
}

let chart2CurrentInterval = "monthly"; // Default interval

function fetchSalesData(interval) {
  fetch(SALES_CHART_API + "&interval=" + interval)
    .then((response) => response.json())
    .then((data) => {
      updateSalesChart(data);
    })
    .catch((error) => console.error("Error fetching data:", error));
}

function changeSalesInterval(interval) {
  chart2CurrentInterval = interval;
  fetchSalesData(interval);
}

function extractLabels(data, interval) {
  return data.map((entry) => {
    switch (interval) {
      case "monthly":
        return entry.month;
      case "weekly":
        return entry.week;
      case "daily":
        return entry.day;
      default:
        throw new Error("Invalid interval specified");
    }
  });
}

function updateSalesChart(data) {
  const labels1 = extractLabels(data.jobseekers, chart2CurrentInterval);
  const labels2 = extractLabels(data.recruiters, chart2CurrentInterval);
  // return new labels
  let labels = [...new Set([...labels1, ...labels2])];
  labels.sort((a, b) => {
    if (chart2CurrentInterval !== "monthly") {
      return a.localeCompare(b);
    }
    const weekNumberA = parseInt(a.match(/\d+/), 10);
    const weekNumberB = parseInt(b.match(/\d+/), 10);
    return weekNumberA - weekNumberB;
  });

  const jobseekerData = data.jobseekers.map((entry) => entry.count);
  const recruiterData = data.recruiters.map((entry) => entry.count);

  const ctx = document.getElementById("chart_2").getContext("2d");

  if (window.salesChart instanceof Chart) {
    window.salesChart.destroy();
  }

  window.salesChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Jobseekers",
          data: jobseekerData,
          borderColor: "#007bff",
          backgroundColor: "#007bff",
          // pointBorderColor: "#007bff",
          // pointBackgroundColor: "#007bff",
        },
        {
          label: "Recruiters",
          data: recruiterData,
          borderColor: "#ced4da",
          backgroundColor: "#ced4da",
          // pointBorderColor: "#ced4da",
          // pointBackgroundColor: "#ced4da",
        },
      ],
    },
    options: {
      maintainAspectRatio: true,
      indexAxis: "x",
      scales: {
        x: {
          grid: {
            display: false,
          },
          ticks: {
            display: true,
            maxRotation: 0,
          },
        },
        y: {
          grid: {
            display: false,
          },
          ticks: {
            display: true,
            precision: 0,
          },
        },
      },
      responsive: true,
      plugins: {
        legend: {
          position: "top",
        },
        title: {
          display: false,
          text: "Sales",
          font: {
            size: 16,
          },
        },
        tooltip: {
          mode: "index",
          intersect: false,
          backgroundColor: "#ffffff",
          titleColor: "#543453",
          callbacks: {
            labelTextColor: function () {
              return "#543453";
            },
          },
        },
      },
    },
  });
}

// Fetch data initially
fetchData(currentInterval);
// Fetch data initially
fetchSalesData(chart2CurrentInterval);
