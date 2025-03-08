// Variables

// queries the navbar which has the id navLinks in it and takes all <i> tags in it
const navLinks = document.getElementById("navLinks");
const button = document.querySelector("nav:has(#navLinks) i");
var debounce = false;

// Runtime

// used for the hamburger-menu, it changes the hamburger menu to visible and a black block display, in the else it sets opacity to 0 and the timeout waits the "tween time" sets the debounce of the button to false and if it's 0 it turns it to "none" so you can't press buttons.
function toggleMenu() {
    if (debounce)
        return

    debounce = true
    if (navLinks.style.opacity == 0) {
        navLinks.style.display = "block";
        document.body.offsetHeight;
        navLinks.style.opacity = 1;

    } else {
        navLinks.style.opacity = 0;
    }

    window.setTimeout(() => {
        debounce = false
        if (navLinks.style.opacity == 0)
            navLinks.style.display = "none"; 
    }, 500)
}

// event for when you click, it fires toggleMenu();
button.addEventListener("click", toggleMenu)
