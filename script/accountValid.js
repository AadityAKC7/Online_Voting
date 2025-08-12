// accountValid.js

document.addEventListener('DOMContentLoaded', function() {
    const accNumberInput = document.getElementById('accNumber');
    const accNumberStatus = document.getElementById('accNumberStatus');
    const bankNameInput = document.getElementById('bankName');
    const accNumberError = document.getElementById('accNumberError');
    const bankNameError = document.getElementById('bankNameError');

    accNumberInput.addEventListener('blur', checkAccountNumber);
    bankNameInput.addEventListener('change', validateAccountNumber);

    function checkAccountNumber() {
        const accNumber = accNumberInput.value.trim();
        accNumberStatus.innerText = '';

        if (accNumber) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_account.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText === 'exists') {
                        accNumberStatus.innerText = 'Account number already exists.';
                    } else {
                        accNumberStatus.innerText = '';
                    }
                }
            };
            xhr.send('accNumber=' + encodeURIComponent(accNumber));
        }
    }

    function validateAccountNumber() {
        const accNumber = accNumberInput.value.trim();
        const bankName = bankNameInput.value;
        accNumberError.innerText = '';

        if (bankName === "NIC Asia") {
            if (!accNumber.match(/^\d{15}$/)) {
                accNumberError.innerText = 'Account number must be 15 digits for NIC Asia Bank.';
            } else if (!accNumber.startsWith('25675225')) {
                accNumberError.innerText = 'Account number must start with "25675225" for NIC Asia Bank.';
            }
        } else {
            if (!accNumber.match(/^\d{16}$/)) {
                accNumberError.innerText = 'Account number must be 16 digits for other banks.';
            } else {
                switch (bankName) {
                    case 'Banijya Bank':
                        if (!accNumber.startsWith('45678567')) {
                            accNumberError.innerText = 'Account number must start with "45678567" for Badigya Bank.';
                        }
                        break;
                    case 'Kumari Bank':
                        if (!accNumber.startsWith('88070100')) {
                            accNumberError.innerText = 'Account number must start with "88070100" for Kumari Bank.';
                        }
                        break;
                    default:
                        accNumberError.innerText = 'Please select a bank.';
                }
            }
        }
    }

    window.validateForm = function() {
        const accNumber = accNumberInput.value.trim();
        const bankName = bankNameInput.value;

        validateAccountNumber();

        let valid = true;
        if (accNumberError.innerText || accNumberStatus.innerText === 'Account number already exists.') {
            valid = false;
        }
        if (!bankName) {
            bankNameError.innerText = 'Please select a bank.';
            valid = false;
        }

        return valid;
    };
});
