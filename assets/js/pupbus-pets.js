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

  function setupApplyNowForm() {
    var form = document.getElementById("pupbus-pets-apply-form");
    var formCard = document.querySelector(".pupbus-pets-apply-form-card");
    var successCard = document.querySelector(".pupbus-pets-apply-card--success");

    var params = new URLSearchParams(window.location.search);
    var message = params.get("pupbus_pets_message");
    if (message === "apply_success" && formCard && successCard) {
      formCard.classList.add("pupbus-pets-hidden");
      successCard.classList.remove("pupbus-pets-hidden");
    }
  }

  function setupProfileEdit() {
    var editBtn = document.getElementById('pupbus-pets-edit-profile-btn');
    var cancelBtn = document.getElementById('pupbus-pets-cancel-edit-btn');
    var editActions = document.querySelector('.pupbus-pets-profile-edit-actions');
    var viewTexts = document.querySelectorAll('.pupbus-pets-profile-view-text');
    var editInputs = document.querySelectorAll('.pupbus-pets-profile-edit-input');

    if (!editBtn) return;

    function showEditMode() {
      editBtn.style.display = 'none';
      viewTexts.forEach(function(el) { el.style.display = 'none'; });
      editInputs.forEach(function(el) { el.style.display = 'block'; });
      if (editActions) editActions.style.display = 'flex';
    }

    function hideEditMode() {
      editBtn.style.display = 'inline-flex';
      viewTexts.forEach(function(el) { el.style.display = 'inline'; });
      editInputs.forEach(function(el) { el.style.display = 'none'; });
      if (editActions) editActions.style.display = 'none';
    }

    editBtn.addEventListener('click', showEditMode);
    
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        hideEditMode();
      });
    }

    hideEditMode();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function() {
      setupTabs();
      setupApplyNowForm();
      setupProfileEdit();
    });
  } else {
    setupTabs();
    setupApplyNowForm();
    setupProfileEdit();
  }
})();
