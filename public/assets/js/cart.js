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

        if (parseInt(data.count) > 0) {
            cartCount.style.display = "inline-grid";
        } else {
            cartCount.style.display = "none";
        };
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

        showToast(data.message, "success");
    }
});

function showToast(message, type = "success") {
    let toast = document.createElement("div");

    toast.className = `toast-message ${type}`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add("show");
    }, 100);

    setTimeout(() => {
        toast.classList.remove("show");

        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 2500);
}

if (window.toastMessage) {
    showToast(window.toastMessage, window.toastType || "success");
}

updateCartCount();