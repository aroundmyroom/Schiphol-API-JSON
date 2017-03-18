# Schiphol-API-JSON
This readme is part of development so can and will change

Schiphol is the Dutch airport and they have made their data public through their API

I have chosen to get the data as JSON and try to make something with PHP

At this time only some JSON is outputted and it needs to be put in HTML where options can be selected

Please be aware: This author as totally no clue about coding but wants a number of things:

Get data from the public Schiphol API and together we make a nice wepage of it so that users can lookup their flight and even find
after landing their luggage belt
Goal is to enter your flight number and whops .. there is the information
Want to know what flights will be landed.. enter time etc.. In principle all available data is worth something.

Get a list of flighs departing and arriving at schiphol and get all the interesting data shown

Any data obtained from the Flight API can be stored for a maximum of 24 hours. 
It is not allowed to cache or store these data for a longer period.

Getting API access is free. You will get it at https://developer.schiphol.nl

Please help me make a nice tool with it

example data of one arrival

stdClass Object
(
    [flights] => Array
        (
            [0] => stdClass Object
                (
                    [id] => 121377240067832674
                    [flightName] => OR580
                    [scheduleDate] => 2017-03-18
                    [flightDirection] => A
                    [flightNumber] => 580
                    [prefixIATA] => OR
                    [prefixICAO] => TFL
                    [scheduleTime] => 15:05:00
                    [serviceType] => J
                    [mainFlight] => OR580
                    [codeshares] => 
                    [estimatedLandingTime] => 2017-03-18T14:57:23.000+01:00
                    [actualLandingTime] => 2017-03-18T14:57:23.000+01:00
                    [publicEstimatedOffBlockTime] => 
                    [actualOffBlockTime] => 
                    [publicFlightState] => stdClass Object
                        (
                            [flightStates] => Array
                                (
                                    [0] => LND
                                )

                        )

                    [route] => stdClass Object
                        (
                            [destinations] => Array
                                (
                                    [0] => TFS
                                )

                        )

                    [terminal] => 3
                    [gate] => D60
                    [baggageClaim] => stdClass Object
                        (
                            [belts] => Array
                                (
                                    [0] => 18A
                                )

                        )

                    [expectedTimeOnBelt] => 
                    [checkinAllocations] => 
                    [transferPositions] => 
                    [aircraftType] => stdClass Object
                        (
                            [iatamain] => 73H
                            [iatasub] => 73H
                        )

                    [aircraftRegistration] => PHTFC
                    [airlineCode] => 231
                    [expectedTimeGateOpen] => 
                    [expectedTimeBoarding] => 
                    [expectedTimeGateClosing] => 
                    [schemaVersion] => 3
                )