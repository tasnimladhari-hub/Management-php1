document.addEventListener("DOMContentLoaded", () => {

    // ── Card fade-in ─────────────────────────────────────────
    document.querySelectorAll(".card, .product-card").forEach((card, i) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(22px)";
        setTimeout(() => {
            card.style.transition = "opacity 0.55s ease, transform 0.55s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, i * 100);
    });

    // ── Live search (brands grid) ─────────────────────────────
    const searchInput = document.getElementById("search");
    const noResults   = document.getElementById("noResults");

    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const q = searchInput.value.toLowerCase().trim();
            let visible = 0;

            document.querySelectorAll(".card").forEach(card => {
                const matches = card.dataset.name?.includes(q) || card.dataset.cat?.includes(q) || card.innerText.toLowerCase().includes(q);
                card.style.display = matches ? "" : "none";
                if (matches) visible++;
            });

            if (noResults) noResults.style.display = visible === 0 ? "block" : "none";
        });
    }

    // ── Auto-hide flash alerts ───────────────────────────────
    document.querySelectorAll(".alert-success").forEach(el => {
        setTimeout(() => {
            el.style.transition = "opacity .5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        }, 4000);
    });

    // ── Image preview for file inputs ────────────────────────
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener("change", () => {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                let preview = input.parentNode.querySelector(".img-preview");
                if (!preview) {
                    preview = document.createElement("img");
                    preview.className = "img-preview";
                    preview.style.cssText = "display:block;margin-top:10px;width:100px;height:100px;object-fit:cover;border-radius:10px;border:2px solid #e8e0f4;";
                    input.parentNode.appendChild(preview);
                }
                preview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    });

});
