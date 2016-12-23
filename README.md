# Hotel Test - Sito di prova

Questo sito è una prova usando Zend 1 + Doctrine 1. 
Il sito simula l'autenticazione e gestione di ostelli.

Documentazione della banca dati, modello E/R, ecc. si trova nella cartella /docs.

## Configurazione Virtual Host

```
<VirtualHost *:80>
   DocumentRoot "/var/www/easisoft_hotel/public"
   ServerName easisoft_hotel.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "/var/www/easisoft_hotel/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>
```
