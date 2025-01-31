document.addEventListener('DOMContentLoaded', () => {
    console.log('JavaScript loaded!');

    // Example functionality for interactive elements
    const addToCartButtons = document.querySelectorAll('form button[type="submit"]');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Item added to cart!');
        });
    });

    // More JS functionality can go here as needed
});
