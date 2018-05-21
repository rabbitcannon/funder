<p align="center"><img src="http://eos-mc.dev.gamelogic.com/img/eos-dawn.gif"></p>


## An EOS Service

EOS services are designed to interoperate for the purpose of delivering
rewards to lottery players. They have a common framework, largely implemented
in the sciplay/eos-common composer repo, to handle issues like:
- Configuration of runtime options based on schema
- Configuration of peer service endpoints and authentication
- API authentication via OAuth2 (Passport)
- Configuration and monitoring of scheduled jobs
- Tracking of service calls using correlation IDs and Redis tracing
- A common approach to logging and exception handling
- A unified approach to Swagger documentation of APIs
- Database table automatic archiving/sharding
