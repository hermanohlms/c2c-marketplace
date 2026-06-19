document.querySelectorAll('.cart-quantity-input').forEach(input => {

    input.addEventListener('change', async function () {

        const quantity = parseInt(this.value);
        const stock = parseInt(this.dataset.stock);

        if (quantity > stock) {

            alert(`Only ${stock} items available in stock.`);

            this.value = stock;

            return;
        }

        const formData = new FormData();

        formData.append('action', 'ajax-update-cart');
        formData.append('product_id', this.dataset.productId);
        formData.append('quantity', quantity);

        const response = await fetch('/index.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result.success) {
            alert(result.message);
            location.reload();
            return;
        }

        location.reload();
    });

});