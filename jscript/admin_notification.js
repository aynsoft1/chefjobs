document.addEventListener("DOMContentLoaded", (event) => {
  const bellIconBtn = document.getElementById("bellIconId");
  const notificationValue = document.getElementById("notificationValue");
  const NOTIFICATION_API_URL = "../api/admin-notification.php";

  // Function to hit the total API
  function hitTotalAPI() {
    fetch(NOTIFICATION_API_URL + "?q=totalNotification", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        notificationValue.innerHTML = data.total;
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  function updateAPI() {
    // Example of updating the API
    fetch(NOTIFICATION_API_URL + "?q=markAllRead", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        notificationValue.innerHTML = data.total;
      })
      .catch((error) => {
        console.error("Error updating API:", error);
      });
  }

  // Hit the total API on page load
  // hitTotalAPI();

  bellIconBtn.addEventListener("click", () => {
    updateAPI();
  });
});
