# Sito Web 3DTERMOEDIL

Sito web professionale per l'azienda 3D Termoedil, specializzata in edilizia e manutenzione.

## Caratteristiche

- Design moderno e responsivo
- Navigazione fluida con scroll animato
- Sezioni dedicate: Hero, Chi Sono, Servizi, Contatti
- Modulo di contatto con gestione PHP
- Animazioni e transizioni fluide
- Ottimizzato per SEO e performance
- Compatibile con tutti i dispositivi (desktop, tablet, mobile)

## Struttura del Progetto

```
3dtermoedil-website/
├── index.html          # Pagina principale
├── styles.css          # Stili CSS
├── script.js           # JavaScript per interazioni
├── contact.php         # Script PHP per gestione contatti
├── .htaccess          # Configurazione server Apache
├── images/            # Directory delle immagini
│   ├── logo.png
│   ├── hero_background.jpg
│   ├── chisiamo_image.jpg
│   ├── servizi_background.jpg
│   └── contatti_image.jpg
└── README.md          # Questo file
```

## Tecnologie Utilizzate

- **HTML5**: Struttura semantica del sito
- **CSS3**: Stili, animazioni e layout responsivo
- **JavaScript**: Interazioni dinamiche e animazioni
- **PHP**: Gestione del modulo di contatto
- **Google Fonts**: Font Montserrat

## Sezioni del Sito

### 1. Header
- Logo aziendale
- Menu di navigazione (Chi sono?, Contatti)
- Bottone "Prenota Ora"
- Design fisso con effetto scroll

### 2. Hero Section
- Immagine di sfondo a schermo intero
- Titolo principale: "Determinati, dinamici, diversificati"
- Descrizione dell'azienda
- Call-to-action "Scopri di più"
- Effetto parallax

### 3. Chi Sono
- Presentazione dell'azienda e del fondatore Daniel Vraci
- Descrizione dei valori e della professionalità
- Immagine rappresentativa
- Layout a due colonne

### 4. Servizi
- Griglia di servizi offerti:
  - Ristrutturazione
  - Infissi e serramenti
  - Zanzariere
  - Pulizie civili, condominiali e industriali
- Card interattive con hover effect
- Descrizioni dettagliate

### 5. Contatti
- Informazioni di contatto dirette (email, telefono)
- Modulo di contatto con campi:
  - Nome (obbligatorio)
  - Cognome (obbligatorio)
  - Email (obbligatorio)
  - Messaggio (obbligatorio)
- Validazione lato client e server
- Invio email con PHP
- Immagine decorativa

### 6. Footer
- Posizione: Via C. Colombo 25, Ponsacco
- Partita IVA: 02527210500 BA6ETTI
- Contatti: email e telefono

## Funzionalità JavaScript

- **Smooth Scrolling**: Navigazione fluida tra le sezioni
- **Header Dinamico**: Effetto scroll con riduzione dimensioni
- **Animazioni al Scroll**: Elementi che appaiono durante lo scroll
- **Parallax Effect**: Effetto parallasse sulla hero section
- **Form Validation**: Validazione in tempo reale dei campi del form
- **Hover Effects**: Effetti interattivi sulle card dei servizi

## Gestione Contatti (PHP)

Il file `contact.php` gestisce l'invio delle email dal modulo di contatto:

- Validazione e sanitizzazione dei dati
- Protezione anti-spam (honeypot, time check, limit rate)
- Invio email in formato HTML
- Email di conferma automatica al mittente
- Gestione sessioni per limitare gli invii
- Risposta JSON per integrazione AJAX

## Configurazione Server (.htaccess)

- Compressione Gzip per migliorare le performance
- Cache del browser per risorse statiche
- Headers di sicurezza (X-Frame-Options, X-XSS-Protection, etc.)
- Protezione file sensibili
- Gestione errori personalizzata

## Ottimizzazioni

- **Performance**:
  - Immagini ottimizzate
  - CSS e JS minificati (pronto per produzione)
  - Lazy loading per le immagini
  - Cache del browser abilitata

- **SEO**:
  - Struttura HTML semantica
  - Meta tags appropriati
  - URL puliti
  - Contenuto ottimizzato

- **Accessibilità**:
  - Contrasto colori adeguato
  - Navigazione da tastiera
  - Alt text per le immagini
  - Form labels appropriati

## Design Responsivo

Il sito è completamente responsivo e si adatta a:
- **Desktop**: Layout completo con tutte le funzionalità
- **Tablet**: Layout adattato con grid responsive
- **Mobile**: Menu hamburger, layout a colonna singola

### Breakpoints:
- Desktop: > 768px
- Tablet: 481px - 768px
- Mobile: < 480px

## Colori del Brand

- **Arancione principale**: #d97847 (bottoni, accenti)
- **Arancione scuro**: #c56635 (hover states)
- **Arancione chiaro**: #ff6633 (sezione contatti)
- **Grigio chiaro**: #f5f5f5 (sfondi)
- **Bianco**: #fff
- **Nero/Grigio scuro**: #333 (testi)

## Font

- **Font principale**: Montserrat (Google Fonts)
- **Pesi utilizzati**: 300, 400, 600, 700

## Installazione e Deployment

### Requisiti:
- Server web (Apache/Nginx)
- PHP 7.0 o superiore
- Supporto per invio email (mail() function)

### Istruzioni:
1. Carica tutti i file sul server web
2. Assicurati che la directory abbia i permessi corretti (755)
3. Configura l'email di destinazione in `contact.php` (riga 9)
4. Verifica che il server supporti l'invio di email
5. Testa il modulo di contatto

## Configurazione Email

Per configurare l'indirizzo email di destinazione, modifica il file `contact.php`:

```php
$to_email = "tuo-indirizzo@example.com";
```

## Browser Supportati

- Chrome (ultime 2 versioni)
- Firefox (ultime 2 versioni)
- Safari (ultime 2 versioni)
- Edge (ultime 2 versioni)
- Opera (ultime 2 versioni)

## Manutenzione

### Aggiornamento contenuti:
- Testi: modifica `index.html`
- Stili: modifica `styles.css`
- Immagini: sostituisci i file nella directory `images/`

### Backup:
Si consiglia di effettuare backup regolari di:
- Tutti i file del sito
- Database (se implementato in futuro)
- Configurazioni del server

## Sicurezza

Il sito implementa diverse misure di sicurezza:
- Validazione e sanitizzazione input utente
- Protezione CSRF
- Headers di sicurezza HTTP
- Limitazione rate di invio form
- Protezione file sensibili

## Performance

Metriche target:
- First Contentful Paint: < 1.5s
- Time to Interactive: < 3.5s
- Largest Contentful Paint: < 2.5s
- Cumulative Layout Shift: < 0.1

## Licenza

© 2025 3D Termoedil. Tutti i diritti riservati.

## Contatti

Per supporto tecnico o domande sul sito:
- Email: 3dtermoedil@gmail.com
- Telefono: +39 380 658 6158
- Indirizzo: Via C. Colombo 25, Ponsacco

## Crediti

- Design e sviluppo: Basato sul sito originale Squarespace
- Font: Google Fonts (Montserrat)
- Immagini: Stock images e materiale fornito dal cliente

