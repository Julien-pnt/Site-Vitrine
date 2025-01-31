// Retrieve the cart from localStorage or initialize an empty array if not found
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Function to add an item to the cart
function addToCart(name, price, image) {
    // Check if the item is in stock
    if (!checkStock(name)) {
        alert('Ce produit est en rupture de stock.');
        return;
    }

    // Find if the item already exists in the cart
    const existingItem = cart.find(item => item.name === name);

    // If the item exists, increase its quantity
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        // If the item does not exist, add it to the cart with quantity 1
        cart.push({ name, price, image, quantity: 1 });
    }

    // Update the stock of the item
    updateStock(name);
    // Save the updated cart to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    // Update the cart icon to reflect the new total items
    updateCartIcon();
    // Animate the cart icon to give feedback to the user
    animateCartIcon();
    // Show a toast notification to the user
    showToast(`${name} a été ajouté au panier.`);
}

// Function to update the cart icon with the total number of items
function updateCartIcon() {
    const cartCount = document.querySelector('.cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
}

// Function to animate the cart icon
function animateCartIcon() {
    const cartIcon = document.querySelector('.cart-icon');
    cartIcon.classList.add('bounce');
    setTimeout(() => cartIcon.classList.remove('bounce'), 500);
}

// Function to show a toast notification
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    toastMessage.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// Event listener for adding items to the cart
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('add-to-cart')) {
        const name = e.target.getAttribute('data-name');
        const price = parseFloat(e.target.getAttribute('data-price'));
        const image = e.target.getAttribute('data-image');
        addToCart(name, price, image);
    }
});

// Initial update of the cart icon when the page loads
updateCartIcon();