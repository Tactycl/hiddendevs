// Vars

// splits the path name from users/1/profile to users, 1, profile
const pathParts = window.location.pathname.split("/");

// queries some stuff to be used later when showing the profile
const usernameText = document.querySelector(".profile-item #username");
const descriptionText = document.querySelector(".profile-item #description");
const iconImage = document.querySelector(".profile-item #icon");
const bannerImage = document.querySelector(".sub-header");

// uploads URL prefix
const uploadsURL = "https://api.tarylem.com/v1/web/uploads/";
var userId = null;

// Profile

// takes the second path part (userid)
if (pathParts[1] === "users" && pathParts[3] === "profile") {
    userId = +pathParts[2];
}

// fetch URL from loadgames.js
async function fetchUrl(url) {
    return await fetch(url, {
        method: "POST",
    }).then(
        response => {
            return response.json();
        }
    ).catch(
        error => {
            console.error("Fetch Error:", error);
        }
    )
}

// sets up the profile so you can see it's an invalid user id, try: https://www.tarylem.com/users/2/profile
function loadInvalidUser() {
    usernameText.textContent = "Invalid User";
    descriptionText.textContent = "none";
}

// loads a user profile with the fetch function, if it errors (no result) it loads the invalid user. If success it sets all data. Try: https://www.tarylem.com/users/1/profile
async function loadUser(userId) {
    const result = await fetchUrl("https://api.tarylem.com/v1/web/profile.php?id=" + userId);
    if (result.status === "error") {
        loadInvalidUser();
        return;
    }

    const data = result.data;
    usernameText.textContent = data["username"];
    descriptionText.textContent = data["description"];

    if (data["icon_url"] != null) {
        iconImage.style.backgroundImage = "url(" + uploadsURL + data["icon_url"] + ")";
    }
    if (data["banner_url"] != null) {
        bannerImage.style.backgroundImage = "url(" + uploadsURL + data["banner_url"] + ")";
    }
}

// Checks if a user id exists, if yes it loads, if no it loads invalid.
if (userId != null) {
    loadUser(userId);

} else {
    loadInvalidUser();
}
