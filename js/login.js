function notify(text) {
  Toastify({
    text: text,
    duration: 1000,
    destination: "https://github.com/apvarun/toastify-js",
    newWindow: true,
    // close: true,
    gravity: "top", // `top` or `bottom`
    position: "center", // `left`, `center` or `right`
    stopOnFocus: true, // Prevents dismissing of toast on hover
    style: {
      background: "linear-gradient(to right, #00b09b, #96c93d)",
    },
    onClick: function () {}, // Callback after click
  }).showToast();
}

$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const params = urlParams.get("email");
  if (params) {
    $("#email").val(params);
  }

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

      const logoButton = document.querySelector(".logo");
      if (logoButton) {
        logoButton.addEventListener("click", function () {
          window.location.href = "index.html";
        });
      }
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

  $("#login-form").submit(function (e) {
    e.preventDefault();

    var email = $("#email").val();
    var password = $("#password").val();

    $.ajax({
      type: "POST",
      url: "http://127.0.0.1/GUVI-login/php/login.php",
      data: {
        email: email,
        password: password,
      },
      success: function (response) {
        response = JSON.parse(response);
        console.log(response);
        if (response.message === "Login successful") {
          localStorage.setItem("userId", response.user.id);
          localStorage.setItem("email", response.user.email);
          localStorage.setItem("password", response.user.password);
          window.location.href =
            "Profile.html?id=" + localStorage.getItem("userId");

          $("#email").val("");
          $("#password").val("");
        } else if (response.message === "user does not exists") {
          notify("user not found");
        } else if (response.message === "Incorrect password") {
          notify("Incorrect password");
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  });
});
