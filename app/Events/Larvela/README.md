# Larvela Message Events

These events are dispatched when consumer actions occur in the store. You dont need to pre-register them, just dispatch them at
strategic points in the code.

Larvale Message Events are designed to enable realtime tracking of the Business Processes so a Metrics Dashboard can
be built to monitor the internal flow of the store independant of the typical metrics you get by looking at the Linux Process Level.

## Status

Alpha development phase at present.

## Overview

The MessageTemplate class is an abstract class that contains the common dispatch() method that calls the derived class to implement and return the JSON data from the parameters that were passed to it.

The dispatch mechanism in the Message Template will then kick off a Job and dispatch as per the prefered transport method,
which can be disabled as needed.

Ideally a Message Queue system or Elastic search implementation can push the JSON data that is returned to an external system.

## Proposed Usage

```php
 $msg = new AddToCartMessage($store, $cart etc);
 $msg->dispatch();
```
