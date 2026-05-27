async function updateWishlistCount() {
    const formData = new FormData();
    formData.append("action", "wishlist-count");

    const response = await fetch("/public/index.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();

    const wishlistCount = document.getElementById("wishlist-count");

    if (wishlistCount) {
        wishlistCount.textContent = data.count;

        if (parseInt(data.count) > 0) {
            wishlistCount.style.display = "inline-grid";
        } else {
            wishlistCount.style.display = "none";
        }
    }
}

document.addEventListener("submit", async function (event) {
    const form = event.target;

    if (!form.classList.contains("ajax-wishlist")) {
        return;
    }

    event.preventDefault();

    const formData = new FormData(form);
    formData.append("ajax", "1");

    const response = await fetch("/public/index.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();

    if (data.success) {
        await updateWishlistCount();

        if (typeof showToast === "function") {
            showToast(data.message, "success");
        }
    } else {
        if (typeof showToast === "function") {
            showToast(data.message || "Wishlist update failed.", "error");
        }
    }
});

updateWishlistCount();