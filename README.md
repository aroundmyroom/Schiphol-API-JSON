# Schiphol-API-JSON

a lot has changed with the Schiphole API and this is not in use anymore. It was an expiriment to see if I could ' code ' .. this script worked, but too much hassle
therefore in 2022 I decided to archive this repository


Update APRIL 2019
Schiphol heeft de API bijgewerkt naar versie 4
Hierdoor waren aanpassingen nodig om deze applicatie weer bijgewerkte data te kunnen laten zien

Aangepast:
- de aanroep van de de API. API gegevens via headers doorsturen
Tikfout in het voorbeeld script van developer.schiphol.nl gevonden en doorgegeven.
Omdat niets meer werkte wil je via een voorbeeldscript de data binnen krijgen zodat je van daaruit verder kan bouwen
alleen dat ging niet omdat de API key niet door kwam (een :  was vergeten in het voorbeeld script)

- de gebruikte URL's aangepast omdat bepaalde variabelen nu stricter gevraagd worden waaronder 'scheduleDate'
- Bij het opvragen van een tijd moet de datum mee, dit is toegevoegd
- meer debug informatie verwerkt in de scripts zodat het zoeken wat makkelijker gaat




March 2017
Begonnen met niets... (en ik kan ook niet programmeren), dus weet ik wat ik doe? waarschijnlijk niet
Credits: met dank aan Peter aka Zaph voor aanpassingen en tips die ik heb kunnen gebruiken.

Is de code gestructureerd? Kan vast en zeker beter. Code is compleet met NANO (linux) geschreven.

Met hulp van Google.com en heel veel verschillende resultaten is het volgende inmiddels gerealiseerd:

- Data wordt opgehaald van de API van Schiphol in JSON formaat. 
- Data wordt dan vertaald naar variabelen, hiervoor was hulp vanuit tweakers.net beschikbaar. Als nono wordt je wel met de neus op de
  feiten gedrukt omdat er verwacht wordt dat je snapt wat je doet (uh uhhhh) .. 

- Nadat de de json goed geparsed cq. decoded kon worden ervoor gezorgd dat deze data in de browser kwam
- Spelen met if, elseif, foreach etc.. hier zitten nog wat zwarte gaten omdat niet elke functie (bijv bij een 'case' functie met default)
  helder is.

- Daarna goed gekeken naar schipholapp en ook de basis van de schipholapp gevonden op github (zoek op flightboard en je vind code uit 2012).
  Deze code heb ik gebruikt om de basis van de CSS in de code te zetten

- Hierin ligt de uitdaging om vanuit een tabelstructuur de php informatie / variabelen te krijgen

- Echte f*ckups gemaakt? Ja hoor, een commit met daarin mijn API gegevens .. OEPS.. maar
  een 'git reset --hard [commitnumber]
  en dan een git push origin HEAD --force zorgde dat de commit verwijderd werd want je kan wel reverten, maar dat helpt niet om je persoonlijke 
  gegevens kwijt te raken

- Waarom doe ik dit? Gewoon om te kijken wat ik kan en ik wil vaak niet afhankelijk zijn van andere websites en als iemand wil helpen, graag

