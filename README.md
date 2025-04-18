# WhatsApp Cart per PrestaShop 8 / WhatsApp Cart for PrestaShop 8

## Italiano

Un modulo per PrestaShop 8 che consente ai clienti di inviare il contenuto del proprio carrello via WhatsApp per richiedere un preventivo personalizzato.

### Caratteristiche

- **Richiesta preventivi via WhatsApp**: I clienti possono inviare il contenuto del proprio carrello via WhatsApp al titolare dell'e-commerce
- **Generazione PDF**: Creazione automatica di un PDF con i dettagli del carrello
- **Integrazione WhatsApp Business API**: Invio di messaggi e documenti tramite l'API ufficiale di WhatsApp Business
- **Modalità link diretto**: Possibilità di utilizzare link diretti wa.me senza necessità di API Business
- **Reindirizzamento automatico**: Nella modalità link diretto, reindirizzamento automatico a WhatsApp con countdown
- **Dashboard amministratore**: Interfaccia per gestire le richieste ricevute e rispondere direttamente da back-office
- **Cronologia richieste**: I clienti possono visualizzare lo storico delle proprie richieste di preventivo
- **Conversazioni**: Tracciamento completo delle conversazioni tra cliente e titolare
- **Responsive design**: Ottimizzato per dispositivi mobili con interfaccia adattiva

### Requisiti

- PrestaShop 8.0 o superiore
- PHP 7.4 o superiore
- Account WhatsApp Business API
- Estensione PHP cURL abilitata

### Installazione

1. Scarica il modulo
2. Carica la cartella `mpwacart` nella directory `/modules/` del tuo PrestaShop
3. Vai al pannello di amministrazione > Moduli > Module Manager
4. Cerca "WhatsApp Cart" e clicca su "Installa"

### Configurazione

Dopo l'installazione, è necessario configurare il modulo:

1. Vai al pannello di amministrazione > Moduli > Module Manager > WhatsApp Cart > Configura
2. Scegli la modalità di funzionamento:
   - **Modalità API Business**: Richiede API Key e ID del numero di telefono WhatsApp Business
   - **Modalità link diretto**: Funziona con un semplice link wa.me senza necessità di API
3. Inserisci il numero di telefono del titolare che riceverà le notifiche
4. Personalizza il template del messaggio inviato al cliente
5. Configura le opzioni di visualizzazione (nascondere il pulsante checkout, ecc.)
6. Salva le impostazioni

### Come funziona

1. Il cliente compila il carrello normalmente
2. Al posto del pulsante "Procedi all'ordine", visualizza "Richiedi preventivo su WhatsApp"
3. Il cliente inserisce il proprio numero di telefono e conferma
4. Il sistema genera un PDF con i dettagli del carrello
5. In base alla modalità configurata:
   - **Modalità API Business**: Il sistema invia un messaggio WhatsApp al titolare con il PDF allegato
   - **Modalità link diretto**: Il cliente viene automaticamente reindirizzato a WhatsApp dopo un countdown
6. Il titolare riceve una notifica nel back-office e può rispondere direttamente da WhatsApp
7. La conversazione viene tracciata nel sistema

---

## English

A module for PrestaShop 8 that allows customers to send their cart contents via WhatsApp to request a customized quote.

### Features

- **WhatsApp Quote Requests**: Customers can send their cart contents via WhatsApp to the e-commerce owner
- **PDF Generation**: Automatic creation of a PDF with cart details
- **WhatsApp Business API Integration**: Sending messages and documents through the official WhatsApp Business API
- **Direct Link Mode**: Ability to use direct wa.me links without requiring Business API
- **Automatic Redirection**: In direct link mode, automatic redirection to WhatsApp with countdown
- **Admin Dashboard**: Interface to manage received requests and respond directly from the back-office
- **Request History**: Customers can view the history of their quote requests
- **Conversations**: Complete tracking of conversations between customer and owner
- **Responsive Design**: Optimized for mobile devices with adaptive interface

### Requirements

- PrestaShop 8.0 or higher
- PHP 7.4 or higher
- WhatsApp Business API account
- PHP cURL extension enabled

### Installation

1. Download the module
2. Upload the `mpwacart` folder to the `/modules/` directory of your PrestaShop
3. Go to the admin panel > Modules > Module Manager
4. Search for "WhatsApp Cart" and click "Install"

### Configuration

After installation, you need to configure the module:

1. Go to the admin panel > Modules > Module Manager > WhatsApp Cart > Configure
2. Choose the operating mode:
   - **Business API Mode**: Requires API Key and WhatsApp Business Phone Number ID
   - **Direct Link Mode**: Works with a simple wa.me link without requiring API
3. Enter the owner's phone number that will receive notifications
4. Customize the message template sent to the customer
5. Configure display options (hide checkout button, etc.)
6. Save the settings

### How it works

1. The customer fills the cart normally
2. Instead of the "Proceed to checkout" button, they see "Request quote on WhatsApp"
3. The customer enters their phone number and confirms
4. The system generates a PDF with the cart details
5. Depending on the configured mode:
   - **Business API Mode**: The system sends a WhatsApp message to the owner with the PDF attached
   - **Direct Link Mode**: The customer is automatically redirected to WhatsApp after a countdown
6. The owner receives a notification in the back-office and can respond directly from WhatsApp
7. The conversation is tracked in the system

## Support

For assistance, contact:
- Email: maxx.palermo@gmail.com

## License

Academic Free License version 3.0

## Author

Massimiliano Palermo
