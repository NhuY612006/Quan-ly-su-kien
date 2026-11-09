document.addEventListener("DOMContentLoaded", function () {
    // Trang Mua ve da co day du hieu ung hologram goc, khong tao lop trung lap.
    if (document.querySelector(".ticket-main")) return;

    document.body.classList.add("cyber-ocean-enabled");

    const layer = document.createElement("div");
    layer.className = "cyber-ocean-layer";
    layer.setAttribute("aria-hidden", "true");

    const bubbles = document.createElement("div");
    bubbles.className = "holo-bubbles";
    layer.appendChild(bubbles);

    ["fish-one", "fish-two", "fish-three", "fish-four"].forEach(function (fishClass) {
        const fish = document.createElement("div");
        fish.className = "holo-fish " + fishClass;
        layer.appendChild(fish);
    });

    document.body.prepend(layer);
});
