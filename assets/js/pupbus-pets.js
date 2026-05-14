(function () {
  function setupTabs() {
    var tabControls = document.querySelectorAll("[data-pupbus-pets-tab]");
    var tabButtons = document.querySelectorAll(".pupbus-pets-tab-btn");
    var panels = document.querySelectorAll(".pupbus-pets-tab-panel");

    if (!tabControls.length || !panels.length) {
      return;
    }

    function activateTab(tabName) {
      tabButtons.forEach(function (button) {
        var active = button.getAttribute("data-pupbus-pets-tab") === tabName;
        button.classList.toggle("is-active", active);
      });

      panels.forEach(function (panel) {
        var active = panel.getAttribute("data-pupbus-pets-panel") === tabName;
        panel.classList.toggle("pupbus-pets-hidden", !active);
      });
    }

    tabControls.forEach(function (el) {
      el.addEventListener("click", function (e) {
        var tabName = el.getAttribute("data-pupbus-pets-tab") || "login";
        if (el.tagName === "BUTTON" || el.tagName === "A") {
          e.preventDefault();
        }
        activateTab(tabName);
      });
    });

    var params = new URLSearchParams(window.location.search);
    var message = params.get("pupbus_pets_message");

    if (message === "register_error" || message === "register_success") {
      activateTab("register");
      return;
    }

    activateTab("login");
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", setupTabs);
  } else {
    setupTabs();
  }
})();
