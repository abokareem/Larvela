# Larvela Message Events

Larvale Message Events are designed to enable realtime tracking of the Business Processes so a Metrics Dashboard can
be built to monitor the internal flow of the store independant of the typical metrics you get by looking at the Linux Process Level or what you might see in Google Analytics.

This is not a replacement for Google Analytics!

Larvela events are dispatched when consumer actions occur in the store. You dont need to pre-register them, just dispatch them at strategic points in the code.

The Output format of the event is the selling point, and the transport mechanism can be implemented for your unique solution.

You can implement JSON output and capture it in an Elastic DB, or dump it on a RabbitMQ message queue (if you write the driver for it).


## Status

Initial deployment phase, more work on message JSON format and content. Mechanism for dispatch is working and an initial MQ test implementation has worked very well.

## Overview

The MessageTemplate class is an abstract class that contains the common dispatch() method that calls the derived class to implement and return the JSON data from the parameters that were passed to it.

The dispatch mechanism in the Message Template will then instanciate the required objects to transport the JSON data to the target service end point. End points are defined in the config/app.php file.

Ideally a Message Queue system or Elastic search implementation can push the JSON data that is returned to an external system. You could also just dumpt to a text file or a combination of different mechanism.


## Proposed Usage

```php
 $msg = new AddToCartMessage($store, $cart etc);
 $msg->dispatch();
```
