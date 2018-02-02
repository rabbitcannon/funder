<?php
// Settings Schema Model for this service
// Your service will receive its settings based on this schema via a POST to /api/settings
//
namespace App;

use Exception;
use Illuminate\Support\Facades\Log;

class SettingsSchema
{
    /**
     * Returns the full application schema including all schemas loaded by installed components. Additional
     * namespaces are loaded into the schema via the schema key in the config/setting.php file.
     */
    public static function get()
    {
       $namespaces = config( 'setting.schema.namespaces' );
       $schema = [];
       foreach( $namespaces as $namespace )
       { $schema[ $namespace ] = config( "$namespace.schema" ); }
       return $schema;
    }
    
    /**
     * Convert the schema into a Settings array using the 'value' tags for each leaf element. Base types (text, number, enum)
     * use the value. Nested elements such as group and multigroup require that we descend the tree.
     * 
     * If the full schema has been loaded it can be passed. Otherwise we use the static get method to fetch the schema. This 
     * argument is used to recurse the schema tree and as a side effect allows for any portion of a schema to be converted to
     * its default settings. 
     * 
     * @param array $schema
     */
    public static function getSchemaDefaults( Array &$schema = [] )
    {
      $schema = empty( $schema ) ? self::get() : $schema;
      $response = [];
      foreach( $schema as $key => $node )
      {
        if( empty( $node['type'] ) )
        { throw new Exception("Setting $key is missing required type field."); }
        
        switch ( $node['type'] )
        {
          case 'number':
          case 'text':
          case 'enum':
          case 'multigroup':
                       $response[$key] = isset($node['value']) ? $node['value'] : NULL;
                       break;
          
          case 'group':
                       $response[$key] = empty( $node['fields'] ) ? [] : self::getSchemaDefaults( $node['fields'] );
                       break;

          default:
                       throw new Exception("Setting $key has invalid type: {$node['type']}");
        }
      }
      return $response;
    }
    
}
