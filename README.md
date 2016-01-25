<!-- -*- mode: markdown; coding: utf-8 -*- -->
# Puolueen kannattajakorttien muodostaja

**English summary:** This is a damn simple web application for
generating pre-filled PDFs with given data. The form in question is
for collecting signatures for a new political party in Finland. The
site is live at https://kannatus.liittovaltio.fi/.

Tämä sovellus muodostaa haluamasi puolueen kannattajakortteja
koneellisesti. Palvelu on selaimella käytettävissä osoitteessa
https://kannatus.liittovaltio.fi/ ja tämän lisäksi tarjolla on
rajapinta palvelun käyttämiseksi suoraan.

License: [ISC](http://choosealicense.com/licenses/isc/)

## Rajapintakuvaus

Rajapinta sijaitsee osoitteessa https://kannatus.liittovaltio.fi/ ja
sitä käytetään `HTTP GET` -kutsulla, jonka kenttien perusteella lomake
täytetään. Käytettävän merkistökoodauksen tulee olla UTF-8.

Kenttä | Esimerkki | Kuvaus
------ | --------- | ------
party | Piraattipuolue | Puolueen nimi ilman ry:tä
bday | 1983-03-28 | Syntymäaika ISO-8601 -muodossa
fname | Ville Petteri | Etunimet
lname | Virtanen | Sukunimi
city | Leppävirta | Henkilön kotikunta
location | Leppävirta | Allekirjoituspaikka, tavallisesti sama kuin kotikunta

Rajapintaa saa käyttää vapaasti, mutta tietoliikenteen häirinnän tapauksessa seuraa
bannia ja parhaassa tapauksessa rikosilmoitus.
