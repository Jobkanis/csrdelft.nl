# Stappenplan tot contribueren aan de stek

## Stap 0
Zorg ervoor dat je een database dump krijgt van de PubCie, zonder deze dump is het erg ingewikkeld om de boel draaiende te krijgen. Zorg er ook voor dat je een plaetjes dump hebt.
<br><br>

## Stap 1
Maak een account aan op [GitHub](https://github.com).
<br><br>

## Stap 2
Installeer [git](https://git-scm.com). En vertel git met welke gegevens je aanpassingen wil maken, zorg dat het emailadres overeen komt met het account op GitHub. (Regels met een `$` er voor moeten uitgevoerd worden in powershell/bash)

```
$ git config --global user.name "John Doe"
$ git config --global user.email johndoe@example.com
```

### Stap 2.1
Ben je geen lid van de PubCie, [maak een fork van de stek](https://github.com/csrdelft/csrdelft.nl/fork)
<br><br>

## Stap 3
Download de stek op je computer, als je net een fork hebt gemaakt gebruik dan de url van je zelfgemaakte repository.

```
$ git clone git@github.com:csrdelft/csrdelft.nl
$ cd csrdelft.nl
$ git submodule init
$ git submodule update
```

### Stap 3.1
De hele filestructuur van de repository is nu gedownload op je computer. Een korte uitleg van wat welke folder betekent is te vinden op [deze wikipagina](https://github.com/csrdelft/csrdelft.nl/wiki/Filestructuur)
<br><br>

## Stap 4: Installatie
Er zijn drie mogelijke manieren om te installeren, met Docker, Symfony of met de hand. Als je actief gaat ontwikkelen aan de stek is het met de hand opzetten aan te raden.

### 1. Met de hand
Installeer Apache2 met PHP en MySQL. Op Windows is er XAMPP, wat dit makkelijk maakt.

#### Apache2
Maak in je `hosts` (`/etc/hosts` of `C:\Windows\system32\drivers\etc\hosts`) bestand een verwijzing van `dev-csrdelft.nl` naar `localhost`.
Voeg bijvoorbeeld de volgende regel toe: `127.0.0.1 dev-csrdelft.nl`

De volgende configuratie werkt goed voor Apache2. (**Let op de** `php_value include_path ...`.)

In XAMPP (waarmee altijd XAMPP Control Panel wordt bedoeld): `Apache => config => <Browse>[Apache] => conf => extra => httpd-vhosts.conf` en plak het volgende:
```
<VirtualHost dev-csrdelft.nl:80>
    DocumentRoot "<repo root>\htdocs"
    ServerName dev-csrdelft.nl
    ServerAlias dev-csrdelft.nl
    ErrorLog "logs/dev-csrdelft.nl-error.log"
    #tell php to look in the lib-dir
    php_value include_path "<repo root>\lib"
    <Directory "<repo root>\htdocs">
        AllowOverride All
        Order Allow,Deny
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
```

#### PHP
Enable `ldap` in `php.ini`

In XAMPP: `Apache => config => PHP (php.ini) => Zoek naar ldap => Haal de ; bij ;extension=ldap weg`

#### MySQL
We gaan nu een MySQL server opstarten, waar vervolgens de lokale database op runt.

In XAMPP: `MySQL => start`. Hopelijk start de MySQL server gelijk op. Stel de 3306 poort is bezet, dan zijn er 2 oplossingen:
1. Klik op de Netstat knop in XAMPP, kijk welk process port 3306 bezet houdt en kill dit programma via de Task Manager.
2. [Verander de poort voor de MySQL server](https://stackoverflow.com/questions/32173242/conflicting-ports-of-mysql-and-xampp). In XAMPP: `MySQL => config => my.ini`. Vervang de 3306 poort overal naar een ander poortnummer, bijvoorbeeld 3307. Ga dan in XAMPP zelf naar `config => Service and Port Settings => MySQL Tab` En verander de service naam `mysql` naar `mysqlxampp` en de main port 3306 naar 3307.

In de C:/XAMPP folder, ga naar `mysql\bin`. Open een terminal en typ het volgende commando:
```
./mysql.exe -u root -p
# of
mysql -u root -p (als het mysql commando al wel gedefinieerd is in een windows PATH variable.)
```
Je bent nu ingelogd op de MySQL server.
Maak vervolgens een database `csrdelft` aan.

```
CREATE USER 'csrdelft'@'localhost' IDENTIFIED BY 'bl44t';
CREATE DATABASE `csrdelft`;
GRANT ALL PRIVILEGES ON `csrdelft` . * TO 'csrdelft'@'localhost';
```

Als je database verbinding anders is dan gebruiker `csrdelft` met wachtwoord `bl44t` op host `localhost` en database `csrdelft`, voeg dan de dsn van je database toe aan `.env.local`

Switch nu naar de csrdelft database met het commando: `use csrdelft;`

**Plaats nu de export die je in stap 0 hebt gefixt in de database.** Doe dit als volgt:
```
source <repo root>\data\***.sql (Met *** voor de tabellen file);
source <repo root>\data\###.sql (Met ### voor de data file);
```

#### Dependencies
Download en installeer [Composer](https://getcomposer.org/). Dit wordt gebruikt om de dependencies te installeren.
Open vervolgens een terminal in de projectmap en voer het volgende commando uit.

```bash
composer install
composer migrate (als de csrdelft database al is aangemaakt en de data dump staat erin)
```

Download en installeer [Yarn](https://classic.yarnpkg.com/en/docs/install). Dit wordt gebruikt om JavaScript dependencies te installeren en om JavaScript te builden.

```bash
# Installeer dependencies
$ yarn

# Run build
$ yarn run dev

# Run build en blijf watchen
$ yarn run watch
# of
$ yarn run watch-poll
```

Download en installeer [ImageMagick](https://imagemagick.org/script/download.php). Dit wordt gebruikt om het fotoalbum goed te laten werken. Als je v7 van ImageMagick hebt geinstalleerd voeg dan `IMAGEMAGICK=magick` toe aan `.env.local`

#### Klaar
Ga nu naar `http://dev-csrdelft.nl`

P.S. Iedere keer als je wil gaan programmeren aan de stek, moet je weer `yarn run watch` uitvoeren.

#### Cache (geavanceerd)

Dit is optioneel, maar kan helpen om je dev stek wat sneller te maken of om specifieke cache problemen te kunnen testen. Lees het [Cache](https://github.com/csrdelft/csrdelft.nl/wiki/Caching) document op de wiki voor meer info.

<hr>

### 2. Docker
Installeer [Docker](http://docker.com).

Kopieer de database dump naar de `data` map. Kopieer de plaetjes naar de `htdocs/plaetjes` map. Kopieer eventuele pasfoto's en fotoalbums naar `data/foto`

    # Start alles op, dit duurt de eerste keer ongeveer een kwartier
    $ docker-compose up

Dit zet alles klaar om de stek te runnen. Als dit klaar is kun je naar `http://localhost:8080` navigeren. Aanpassingen worden direct doorgevoerd.

Je kan met de database verbinden op `localhost:3307`, met PhpStorm, HeidiSQL, of wat anders. Met gebruikersnaam `csrdelft`, wachtwoord `bl44t` op database `csrdelft`.

Handige Docker commando's

    # Voeg een php dependency toe
    $ docker-compose run composer require myVendor/package

    # Voer de migraties uit
    $ docker-compose run composer run-script migrate

    # Voeg een npm dependency toe
    $ docker-compose run yarn add myPackage

    # Als de javascript build om de een of andere reden is omgevallen
    $ docker-compose restart yarn
<hr>

### 3. Symfony
Dit is nieuw en werkt nog niet 100% (je kan geen groepen bekijken). Waarschijnlijk wordt dit de manier om de boel te draaien.

Installeer Symfony CLI https://symfony.com/doc/master/cloud/getting-started#installing-the-cli-tool

Installeer een MySQL server zoals staat beschreven bij "Met de hand" en zorg dat deze draait.

Voeg `CSR_DOMAIN=127.0.0.1` en `CSR_ROOT=http://127.0.0.1:8000` toe aan `.env.local`

Voer het volgende commando uit.

```
$ symfony server:start --document-root=htdocs
```

De boel draait nu op `127.0.0.1:8000`
<br><br>

## Stap 5: Development

### Editorconfig
Zorg ervoor dat je editor zo is ingesteld dat de code style (indent/newline types) goed staat ingesteld. Check .editorconfig voor de huidige instellingen.

In PhpStorm kun je de Editorconfig plugin installeren om automatisch de instellingen goed te zetten voor dit project.

### Branches & PR
We hebben enkele branches. Niet heel bijzonder.

- `master` -  live stek, niet stukmaken! Taart als dat wel gebeurd.
- `#issue - naam`: graag issue nummer (van github) vermelden indien mogelijk.
- `naam`: overig

We werken met PRs voor de meeste gevallen. Probeer je code door ten minste 1 persoon te laten reviewen.
Diegene mag hem mergen en zal er vervolgens voor zorgen dat de live stek wordt geupdate.

### Issues
#### Type
Spreekt redelijk voorzich. De kleur is een pastelkleur. De specifieke kleur per label is willekeurig.
`type:enhancement` - Verbetering voor de huidige code base / architectuur. Dit kan bijvoorbeeld een performance verbetering zijn maar ook een refactor.
`type:design` - Puur gericht op uiterlijk.
`type:feature` - Nieuwe toevoeging ten opzichte van huidige code base.
`type:bug`
`type:security`
`type:task`
`type:question`

#### Prioriteit
Het inschatten van dit label kan lastig zijn. Hieronder enkele richtlijnen. De kleuren voor deze labels zijn rood, oranje en geel.
`prio:high` - Moet veranderd worden om de stek draaiende te kunnen houden. Tevens problemen die het gebruik van de stek regelmatig verstoren.
`prio:normal` - Zaken die van waarde zijn maar de huidige stek niet ontregelen als ze niet gefixt worden. Denk hierbij aan nieuwe refactors.
`prio:low` - Veelal issues die vooralsnog genegeerd kunnen worden. Bevatten vaak mooie ideeen, maar daar is nu geen tijd voor / behoefte aan.

#### Component
Het component label is altijd zwart en beschrijft het onderdeel van de stek waar dit over gaat. Deze wordt, in tegenstelling tot de vorige 2, niet consistent gebruikt. Dit zou in een later stadium nog gedaan kunnen worden. Mogelijk zou dit ook met milestones kunnen.
`component:forum`
`component:soccie`
...

### CmsPaginas
De volgende CMS pagina's zijn gehardcoded in de stek, zorg dat deze in de `cms_paginas` tabel aanwezig zijn.

* De lege CMS pagina, alle velden leeg
* `thuis`
* `accountaanvragen`
* `mobiel`
* `UitlegACL`
* `fotostoevoegen`
* `403`
