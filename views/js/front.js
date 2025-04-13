/**
 * WhatsApp Cart Module for PrestaShop
 * 
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2023 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Validazione del numero di telefono
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Rimuovi tutti i caratteri non numerici tranne il +
            let value = this.value.replace(/[^\d+]/g, '');
            
            // Assicurati che ci sia al massimo un +
            const plusCount = (value.match(/\+/g) || []).length;
            if (plusCount > 1) {
                value = value.replace(/\+/g, '');
                value = '+' + value;
            }
            
            // Assicurati che il + sia all'inizio
            if (value.indexOf('+') > 0) {
                value = value.replace(/\+/g, '');
                value = '+' + value;
            }
            
            // Aggiorna il valore
            this.value = value;
        });
    }
    
    // Validazione del form
    const waCartForm = document.getElementById('wacart-form');
    if (waCartForm) {
        waCartForm.addEventListener('submit', function(e) {
            const phoneInput = document.getElementById('phone_number');
            const privacyCheckbox = document.getElementById('privacy_policy');
            
            // Verifica che il numero di telefono sia valido
            if (phoneInput && !isValidPhoneNumber(phoneInput.value)) {
                e.preventDefault();
                alert('Inserisci un numero di telefono valido nel formato internazionale (es. +39XXXXXXXXXX)');
                phoneInput.focus();
                return false;
            }
            
            // Verifica che la privacy policy sia accettata
            if (privacyCheckbox && !privacyCheckbox.checked) {
                e.preventDefault();
                alert('Devi accettare la Privacy Policy per continuare');
                privacyCheckbox.focus();
                return false;
            }
            
            // Mostra un loader
            const submitButton = document.querySelector('.wacart-submit');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="material-icons">hourglass_empty</i> Invio in corso...';
            }
            
            return true;
        });
    }
    
    // Funzione per validare il numero di telefono
    function isValidPhoneNumber(phone) {
        // Deve iniziare con + seguito da almeno 10 cifre
        const phoneRegex = /^\+[0-9]{10,15}$/;
        return phoneRegex.test(phone);
    }
});
