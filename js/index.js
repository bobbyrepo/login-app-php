$(document).ready(function () {
  // adding navbar
  fetch("navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar-container").innerHTML = data;
    })
    .catch((error) =>
      console.error("Error loading the navigation bar:", error)
    );

  // navbar DOM
  fetch("navbar.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("navbar-container").innerHTML = data;

      const logOutButton = document.querySelector(".btn-logout");
      if (logOutButton) {
        logOutButton.addEventListener("click", function () {
          localStorage.clear();
          window.location.href = "index.html";
        });
      }
    })
    .catch((error) =>
      console.error("Error loading the navigation bar:", error)
    );
});
