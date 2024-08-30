document.addEventListener("DOMContentLoaded", (event) => {
  const bellIconBtn = document.getElementById("bellIconId");
  const notificationValue = document.getElementById("notificationValue");

  if (bellIconBtn) {  // Check if the element exists before adding the event listener
    bellIconBtn.addEventListener("click", (event) => {
      event.preventDefault();
      const userType = bellIconBtn.getAttribute("data-user-type");
      OnNotificationIconClicked(userType);
    });
  } 

  function OnNotificationIconClicked(userType) {
    const API_URL =
      userType === "jobseeker"
        ? JOBSEEKER_NOTIFICATION_API_URL
        : userType === "recruiter"
        ? RECRUITER_NOTIFICATION_API_URL
        : null;

    if (API_URL) {
      updateAPI(API_URL);
    } else {
      console.error("Invalid user type for notification");
    }
  }

  function updateAPI(URL) {
    fetch(URL + "?q=markAllRead", {
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
});
