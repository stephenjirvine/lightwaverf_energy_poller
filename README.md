PHP Cacti poller for Lightwave RF Energy Monitor

I bought some LightwaveRF remote control power devices, and an energy monitor. 

The software provided by Lightwave themselves does not allow for logging of historical energy usage, so being familiar with Cacti and not wanting to go to the trouble of writing my own web frontend (or having any of the necessary skill to do so) I wrote a cacti script to do it instead.

This script is designed to be called by Cacti's script server and isn't a great deal of use on it's own.

For a more full featured community-built LightwaveRF API, please see the great work here: http://code.google.com/p/lightwaverf-openapi/