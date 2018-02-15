<?php

// This file documents the calls provided by the eos-common package
// so they appear on the Swagger-UI page. This file is published by
// artisan vendor:publish from eos-common, and you should not edit it.
/**
* @SWG\Get(
*   path="/api/schema",
*   summary="REQUIRED API FOR SERVICES: Return settings pack schema",
*   operationId="settingsSchema",
*   tags={"eos"},
* @SWG\Parameter(
*     name="apikey",
*     in="query",
*     type="string",
*     description="required api key"
*  ),
* @SWG\Response(response=200, description="successful",
*     @SWG\Schema(
*       type="string")
*   ),
* @SWG\Response(response=500, description="internal server error")
* )
*
* @param Request $request
* @return \Illuminate\Http\JsonResponse
*/
class apischemaget {}
/**
* @SWG\Post(
*   path="/api/settings",
*   summary="REQUIRED API FOR SERVICES: Accept settings",
*   operationId="postSettings",
*   tags={"eos"},
* @SWG\Parameter(
*     name="apikey",
*     in="query",
*     type="string",
*     description="required api key"
*  ),
* @SWG\Parameter(
*   in="body",
*   name="body",
*   description="Settings JSON Data",
*   required=true,
*   @SWG\Schema(
*       type="string")
*   ),
* @SWG\Response(response=200, description="successful",
*     @SWG\Schema(
*       type="string")
*   ),
* @SWG\Response(response=500, description="internal server error")
* )
*
* @param Request $request
* @return \Illuminate\Http\JsonResponse
*/
class apischemapost {}
/**
* @SWG\Delete(
*   path="/api/settings",
*   summary="REQUIRED API FOR SERVICES: Delete settings",
*   operationId="deleteSettings",
*   tags={"eos"},
* @SWG\Parameter(
*     name="apikey",
*     in="query",
*     type="string",
*     description="required api key"
*  ),
* @SWG\Response(response=200, description="successful"),
* @SWG\Response(response=500, description="internal server error")
* )
*
* @param Request $request
* @return \Illuminate\Http\JsonResponse
*/
class settingsdel {}
/**
* @SWG\Get(
*     path="/api/oauth/clients",
*     summary="GET OAUTH CLIENTS: only used by eos-mc.",
*     operationId="getOauthClients",
*     tags={"eos"},
* @SWG\Parameter(
*     name="apikey",
*     in="query",
*     type="string",
*     description="required api key"
*  ),
*  @SWG\Response(response=200, description="successful",
*      @SWG\Schema(
*       type="array",
*       @SWG\Items(ref="#/definitions/OauthClientResponse"))
*   ),
*   @SWG\Response(response=500, description="System error")
* )
*/
class clientsget {}
/**
* @SWG\Post(
*     path="/api/oauth/clients",
*     summary="CREATE OAUTH CLIENT: only used by eos-mc.",
*     operationId="createOauthClient",
*     tags={"eos"},
* @SWG\Parameter(
*     name="apikey",
*     in="query",
*     type="string",
*     description="required api key"
*  ),
* @SWG\Parameter(
*     name="body",
*     in="body",
*     description="Client Data",
*     required=true,
*     @SWG\Schema(ref="#/definitions/OauthClientData")
*   ),
*   @SWG\Response(response=200, description="successful",
*      @SWG\Schema(ref="#/definitions/OauthClientResponse")),
*   @SWG\Response(response=500, description="System error")
* )
*/
class clientspost {}
/**
* @SWG\Post(
*   path="/oauth/token",
*   summary="GET AN ACCESS TOKEN: You must know the client credentials id and secret.",
*   operationId="getClientCredentialsToken",
*   tags={"eos"},
* @SWG\Parameter(
*     name="body",
*     in="body",
*     description="Client Authorization",
*     required=true,
*     @SWG\Schema(ref="#/definitions/OauthGrantRequest")
*   ),
*   @SWG\Response(response=200, description="successful",
*     @SWG\Schema(ref="#/definitions/OauthGrantResponse")),
*   @SWG\Response(response=500, description="System error")
*  )
**/
class tokenpost {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"grant_type","client_id","client_secret","scope"}, type="object", @SWG\Xml(name="OauthGrantRequest"))
* @SWG\Property(format="string", property="grant_type", example="client_credentials", description="Grant type")
* @SWG\Property(type="string", property="client_id", example="2", description="ID of the credentials client")
* @SWG\Property(type="string", property="client_secret", example="random-string", description="credentials client secret")
* @SWG\Property(type="string", property="scope", example="manage-keys view-credentials", description="requested scopes")
**/
class OauthGrantRequest {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"access_token","refresh_token","expires_in"}, type="object", @SWG\Xml(name="OauthGrantResponse"))
* @SWG\Property(format="string", property="access_token", example="long-string", description="use as Bearer token")
* @SWG\Property(type="string", property="refresh_token", example="long-string", description="use to refresh expired token")
* @SWG\Property(type="number", property="expires_in", example="445632", description="seconds until expiration")
**/
class OauthGrantResponse {}

/**
* strictly for Swagger doc
* @SWG\Definition(required={"id","name","secret","redirect",}, type="object", @SWG\Xml(name="OauthClientResponse"))
* @SWG\Property(format="number", property="id", example="3", description="client Id")
* @SWG\Property(format="string", property="name", example="eos-wallet", description="name of client")
* @SWG\Property(type="string", property="secret", example="long-string", description="client Secret")
* @SWG\Property(type="string", property="redirect", example="http:url", description="client redirect url")
**/
class OauthClientResponse {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"name","redirect",}, type="object", @SWG\Xml(name="OauthClientData"))
* @SWG\Property(format="string", property="name", example="eos-wallet", description="name of client")
* @SWG\Property(type="string", property="redirect", example="http:url", description="client redirect url")
**/
class OauthClientData {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"name","inbound","outbound"}, type="object", @SWG\Xml(name="ServiceConfig"))
* @SWG\Property(format="string", property="name", example="Bonusing Manager", description="Name of the service")
* @SWG\Property(type="array", property="inbound", description="All known connections to this service", @SWG\Items(ref="#/definitions/ConnectionConfig"))
* @SWG\Property(type="array", property="outbound", description="All known connections from this service", @SWG\Items(ref="#/definitions/ConnectionConfig"))
**/
class ServiceConfig {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"status"}, type="object", @SWG\Xml(name="ServiceConfigResponse"))
* @SWG\Property(format="string", property="status", example="ok", description="Status return, ok or error")
* @SWG\Property(type="string", property="config", example="{json string}", description="Summary of consumed configuration")
* @SWG\Property(type="string", property="message", example="config failure", description="Any error message")
**/
class ServiceConfigResponse {}
/**
* strictly for Swagger doc
* @SWG\Definition(required={"name","url"}, type="object", @SWG\Xml(name="ConnectionConfig"))
* @SWG\Property(format="string", property="name", example="Bonusing Engine", description="Name of the service")
* @SWG\Property(format="string", property="url", example="https://bonusing.pawtest.gamelogic.com", description="URL of the service.")
* @SWG\Property(format="string", property="client_id", example=3, description="Oauth client ID.")
* @SWG\Property(format="string", property="client_secret", example="34fcssec3w", description="Oauth client secret.")
* @SWG\Property(format="string", property="client_callback", example="http://my.client/callback", description="Oauth client redirect url.")
**/
class ConnectionConfig {}