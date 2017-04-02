# Schiphol-API-JSON
Credits: met dank aan Peter aka Zaph voor aanpassingen en tips die ik heb kunnen gebruiken.

March 2017
Begonnen met niets... (en ik kan ook niet programmeren), dus weet ik wat ik doe? waarschijnlijk niet

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

