<p align="center"><img src="http://eos-mc.dev.gamelogic.com/img/eos-dawn.gif"></p>


## LENS (Laravel EOS Notification Service)

LENS is intended to process and relay event notifications to players and agents on a set
of predefined "channels" such as email, push, Slack, sms or other external channels. It 
will handle building and sending notifications to:

- Agents: messages about system events, critical errors, or important lottery events (high tier wins, etc)
- Players: subscription receipts, account events, ticket wins, any other alerts

A UI control panel is provided by LENS to support agent registration and allow agents to 
opt-in to subscribe to specific alert types. All other interaction with LENS is via API (documentation
available at the /api/documentation route).

Events are delivered to LENS by either API call or queue. Player notification events are
typically delivered in a compact "seed" format (just basic DB object identifiers) which LENS
must expand or "hydrate" into full data structures by querying service database schemas.
The fully hydrated model must then either be combined with an installed template and mailed
(or pushed or otherwise delivered in rendered form), or it must be sent as a JSON body to
an external delivery queue, where it will be picked off and rendered by some external service.

Agent notifications are typically simpler to render, but LENS includes features to aggregate
multiple notifications or filter notifications for specific agent subscriptions.
