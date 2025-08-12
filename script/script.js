document.addEventListener('DOMContentLoaded', (event) => {
    const eyeIcon = document.querySelector('.iconBx ion-icon');
    const balanceElement = document.querySelector('.numbers');

    eyeIcon.addEventListener('click', () => {
        if (balanceElement.classList.contains('hidden')) {
            // Show numbers
            balanceElement.classList.remove('hidden');
            balanceElement.textContent = balanceElement.dataset.originalValue;
            eyeIcon.setAttribute('name', 'eye-outline');
        } else {
            // Hide numbers
            balanceElement.classList.add('hidden');
            balanceElement.dataset.originalValue = balanceElement.textContent;
            balanceElement.textContent = '*'.repeat(balanceElement.textContent.length);
            eyeIcon.setAttribute('name', 'eye-off-outline');
        }
    });
});

