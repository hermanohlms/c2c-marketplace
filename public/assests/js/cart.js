async function updateCartCount() {
    const formData = new FormData();
    formData.append("action", "cart-count");

    const response = await fetch("/public/index.php", {
        method: "POST",
        body: formData
    });

    const data = await response.json();

    const cartCount = document.getElementById("cart-count");

    if (cartCount) {
        cartCount.textContent = data.count;
    }
}

document.addEventListener("submit", async function (event) {
    const form = event.target;

    if (!form.classList.contains("ajax-add-to-cart")) {
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
        const cartCount = document.getElementById("cart-count");

        if (cartCount) {
            cartCount.textContent = data.count;
        }

        alert(data.message);
    }
});

updateCartCount();