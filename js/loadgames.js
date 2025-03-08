// Variables

// queries for the <template> in home.php and the table containing it
const gameTemplate = document.querySelector("#game-col-template");
const gameTable = document.querySelector(".games-table");
const Secret = "..."; // Secret here
const gameIds = [
    16867952270, // Dash World GAME ID
    16764997873, // Grabpack System GAME ID
]

// Runtime

// abbreviates numbers like 14000 to 14K
function abbreviateNumber(v) {
    let format = "";
    let suffix = "";

    if (v >= 0 && v < 1000) {
        format = Math.floor(v);
        suffix = "";

    } else if (v >= 1000 && v < 1000000) {
        format = Math.floor(v / 10) / 100;
        suffix = "K";

    } else if (v >= 1000000 && v < 1000000000) {
        format = Math.floor(v / 10000) / 100;
        suffix = "M";

    } else if (v >= 1000000000 && v < 1000000000000) {
        format = Math.floor(v / 10000000) / 100;
        suffix = "B";
    }

    return format + suffix;
}

// simple fetch function, so I don't have to write it out
async function fetchUrl(url) {
    return await fetch(url, {
        method: "POST"
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

// init function called at the bottom, it gets the universe from roblox API and all the data and queries the stat texts in the cloned <template> and changes their text to their stats.
async function init() {
    for (let gameId of gameIds) {
        var universe = await fetchUrl("https://api.tarylem.com/v1/proxy/roblox.php/apis/universes/v1/places/" + gameId + "/universe?Secret=" + Secret);
        universe = universe["universeId"];
        var data = await fetchUrl("https://api.tarylem.com/v1/proxy/roblox.php/games/v1/games?Secret=" + Secret + "&universeIds=" + universe);
        var votes = await fetchUrl("https://api.tarylem.com/v1/proxy/roblox.php/games/v1/games/votes?Secret=" + Secret + "&universeIds=" + universe);
    
        var upvotes = votes["data"][0]["upVotes"];
        var downvotes = votes["data"][0]["downVotes"];
        var likeRatio = Math.round(upvotes / (upvotes + downvotes + 0.001) * 100);
    
        var clone = gameTemplate.content.cloneNode(true);
        clone.querySelector("#game-col").className = "game-col-" + gameId;
        clone.querySelector(".game-title").textContent = data["data"][0]["name"];
        clone.querySelector("#game-stat.playing").textContent = abbreviateNumber(data["data"][0]["playing"]);
        clone.querySelector("#game-stat.visits").textContent = abbreviateNumber(data["data"][0]["visits"]);
        clone.querySelector("#game-stat.favorited").textContent = abbreviateNumber(data["data"][0]["favoritedCount"]);
        clone.querySelector("#game-stat.likes").textContent = likeRatio + "%";
        clone.querySelector(".view-btn").href = "https://www.roblox.com/games/" + gameId;
        gameTable.appendChild(clone);
    }
}

init();
