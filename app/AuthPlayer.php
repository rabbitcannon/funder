<?php

namespace App;

use Illuminate\Auth\AuthenticationException;

// AuthPlayer represents the authenticated player on api calls. This class is also a wrapper (facade) for the Player class.
class AuthPlayer
{
    protected $player_data = [];
    protected $player = NULL;

    /**
     * AuthPlayer constructor.
     *
     * @param $player - registrar_id is the only required input; null is an invalid player
     */
    public function __construct( $player_data = [] )
    {
      if( empty( $player_data['registrar_id'] ) )
      { throw new AuthenticationException( 'Missing required registrar_id' ); }

      $this->player = Player::fetchPlayer( $player_data['registrar_id'] );

      //$player_data must provide all required fields. These come from your applications config/player.php
      foreach( config( 'player.required', [] ) as $attribute )
      {
        if( ! array_key_exists( $attribute, $player_data ) )
        { throw new AuthenticationException( "Missing required player parameter: $attribute" ); }
      }
      $this->player_data = $player_data;
    }

  /**
   * FindOrFail returns an AuthPlayer object or throws exceptions for unauthorized or missing player attributes.
   * PlayerData is aggregated from multiple sources (X-Auth-Spat, Request, etc). These player attributes should
   * not overlap but if they do the value of X-Auth-Spat must control. This prevents callers from overriding player
   * data simply by passing attributes to a GET request.
   *
   * @return AuthPlayer
   */
    public static function fetchOrFail()
    {
      $request = request();

      //first pull player information from the $request, then the X-Auth-Spat header
      $player_data = $request->input( 'player', [] );
      $auth_token = json_decode( $request->header('X-Auth-Spat'), true );
      if( isset( $auth_token['player'] ) && is_array( $auth_token['player'] ) )
      { $player_data = array_merge( $player_data, $auth_token['player']); }

      return new self( $player_data );
    }

  /**
   * Magic override to get player attributes. Player is special in that it is not stored in the $player_data
   * array.
   *
   * @param $name
   */
    public function __get( $name )
    {
      if( $name == 'player' )
      { return $this->player; }

      if( array_key_exists( $name, $this->player_data ) )
      { return $this->player_data[$name]; }

      //if we access an undefined attribute we could return NULL. But caller's cannot determine if the value
      //was set to NULL intentionally. We opt to trigger a runtime error. If you have an optional attribute
      //you should use isset($obj->attrib) before attempting to get the value.
      trigger_error("Undefined attribute $name in AuthPlayer.");
    }

    public function __isset( $name )
    {
      return ( $name == 'player' && $this->player != NULL ) || array_key_exists( $name, $this->player_data );
    }

    /**
     * @return bool
     */
    public function valid() {
        return $this->registrar_id != null;
    }
}
