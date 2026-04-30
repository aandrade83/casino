function handleLogin() {
  const player = document.getElementById("player").value.trim();
  const password = document.getElementById("password").value;

  if (!player || !password) {
    showStatus("error", "Please enter your player and password.");
    return;
  }

  switch (COMPANY_URL) {
    case "bitbet.com":
      checkBitbet(player, password);
      break;

    // Add new cases here manually as needed:
    // case 'othersite.com':
    //     checkOtherSite(player, password);
    //     break;

    default:
      showStatus("error", "Site not configured: " + COMPANY_URL);
  }
}

// ─── Bitbet ──────────────────────────────────────────────────────────────────

function checkBitbet(player, password) {
  showStatus(
    "checking",
    '<span class="spinner"></span> Checking credentials with sportsbook...',
  );
  setBtn(true);

  const payload = { site: "bitbet", player, password };
  console.log("[bitbet] sending request to check.php", payload);

  fetch(MODULE_PATH + "/check.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-API-KEY": "bitbet2026",
    },
    body: JSON.stringify(payload),
  })
    .then((r) => {
      console.log("[bitbet] HTTP status:", r.status, r.statusText);
      return r.text();
    })
    .then((raw) => {
      console.log("[bitbet] raw response body:", raw);
      let data;
      try {
        data = JSON.parse(raw);
      } catch (e) {
        console.error("[bitbet] JSON parse failed:", e.message);
        showStatus("error", "Server returned invalid response. Check console.");
        return;
      }
      console.log("[bitbet] parsed response:", data);
      if (data.success) {
        callController(player);
      } else {
        showStatus(
          "error",
          "✗ Invalid credentials. Please check with your sportsbook.",
        );
      }
    })
    .catch((err) => {
      console.error("[bitbet] fetch failed:", err);
      showStatus("error", "Network error. Could not reach the sportsbook.");
    })
    .finally(() => setBtn(false));
}

// ─── Controller ───────────────────────────────────────────────────────────────

function callController(player) {
  const url = MODULE_PATH + "/controller.php";

  console.log("[controller] calling:", url);

  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "ac=login&player=" + encodeURIComponent(player),
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("[controller] response:", data);

      if (data.success) {
        // 🔥 REDIRECCIÓN AL ACCESS
        window.location.href = "/control/modules/access/index.php";
      } else {
        alert("Login failed: " + (data.reason || "Unknown error"));
      }
    })
    .catch((err) => {
      console.error("Controller error:", err);
      alert("Connection error");
    });
}
// ─── Helpers ─────────────────────────────────────────────────────────────────

function showStatus(type, html) {
  const el = document.getElementById("status");
  el.className = type;
  el.innerHTML = html;
}

function setBtn(disabled) {
  document.getElementById("btn-login").disabled = disabled;
}

// Allow Enter key to submit
document.addEventListener("DOMContentLoaded", () => {
  document.addEventListener("keydown", (e) => {
    if (e.key === "Enter") handleLogin();
  });
});
