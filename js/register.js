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

  $("#registration-form").submit(function (e) {
    e.preventDefault();

    var username = $("#username").val();
    var email = $("#email").val();
    var password = $("#password").val();

    $.ajax({
      type: "POST",
      url: "http://127.0.0.1/GUVI-login/php/register.php",
      data: {
        username: username,
        email: email,
        password: password,
      },
      success: function (response) {
        response = JSON.parse(response);
        console.log(response);
        if (response.message == "User exists") {
          notify("email already exists");
        } else if (response.message == "Registeration successful") {
          notify("Registered successfully!");

          $.ajax({
            type: "POST",
            url: "http://127.0.0.1/GUVI-login/php/profile.php",
            data: {
              id: response.id,
              username: username,
            },
            success: function (profileResponse) {
              console.log(profileResponse);
            },
            error: function (profileError) {
              console.log(profileError);
            },
          });
          window.location.href = "login.html?email=" + email;
          $("#username").val("");
          $("#password").val("");
          $("#email").val("");
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  });
});
