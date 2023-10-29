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

  const ageInput = document.getElementById("age");
  const ageError = document.getElementById("age-error");

  ageInput.addEventListener("input", function () {
    const age = parseInt(ageInput.value, 10);
    if (isNaN(age) || age < 0 || age > 100) {
      ageError.textContent = "Age must be a number between 0 and 100";
    } else {
      ageError.textContent = "";
    }
  });

  const dobInput = document.getElementById("dob");
  const dobError = document.getElementById("dob-error");

  dobInput.addEventListener("change", function () {
    const selectedDate = new Date(this.value);
    const currentDate = new Date();
    if (selectedDate > currentDate) {
      dobError.textContent = "You are not from the future!";
    } else {
      dobError.textContent = "";
    }
  });

  const contactInput = document.getElementById("contact");
  const contactError = document.getElementById("contact-error");

  contactInput.addEventListener("input", function () {
    let contact = this.value;

    // Ensure the length is exactly 10 digits
    if (contact.length === 10) {
      this.value = contact;
      contactError.textContent = "";
    } else {
      contactError.textContent = "Contact must be 10 digits long.";
    }
  });

  const id = localStorage.getItem("userId");
  const email = localStorage.getItem("email");
  const password = localStorage.getItem("password");

  if (id) {
    $.ajax({
      type: "GET",
      url: "http://127.0.0.1/GUVI-login/php/profile.php",
      data: {
        id: id,
        email: email,
        password: password,
      },
      success: function (response) {
        response = JSON.parse(response);
        console.log(response);
        if (response.message == "Incorrect password") {
          window.location.href = "login.html";
        } else {
          const { username, contact, age, dob } = response;
          $("#username").val(username);
          $("#age").val(age);
          $("#dob").val(dob);
          $("#contact").val(contact);
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  }

  $("#update-profile-form").submit(function (e) {
    e.preventDefault();

    var username = $("#username").val();
    var age = $("#age").val();
    var dob = $("#dob").val();
    var contact = $("#contact").val();

    const validateAge = parseInt(age);
    const selectedDate = new Date(dobInput.value);
    const currentDate = new Date();

    if (
      // !isNaN(age) &&
      // age >= 0 &&
      // age <= 100 &&
      // selectedDate <= currentDate &&
      // (contact.length === 10 || contact.length == 0)
      1 == 1
    ) {
      $.ajax({
        type: "PUT",
        url: "http://127.0.0.1/GUVI-login/php/profile.php",
        contentType: "application/json",
        data: JSON.stringify({
          id: id,
          username: username,
          age: age,
          dob: dob,
          contact: contact,
        }),
        success: function (response) {
          response = JSON.parse(response);
          console.log(response);
          if (response.message === "updated successfully") {
            notify("Updated successfully");
          }
          // update name in Register table mysql
          $.ajax({
            type: "PUT",
            url: "http://127.0.0.1/GUVI-login/php/register.php",
            contentType: "application/json",
            data: JSON.stringify({
              id: id,
              username: username,
            }),
            success: function (response) {
              // response = JSON.parse(response);
              console.log(response);
            },
            error: function (error) {
              console.log(error);
            },
          });
        },
        error: function (error) {
          console.log(error);
        },
      });
    }
  });
});
