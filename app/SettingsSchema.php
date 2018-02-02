<?php
// Settings Schema Model for this service
// Your service will receive its settings based on this schema via a POST to /api/settings
//
namespace App;

use Exception;
use App\Exceptions\SettingException;
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
            
            switch( $node['type'] )
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
    
    /**
     * Validate the $schema, or if not provided validate the current installed schema. Returns TRUE if schema is valid and
     * throws detailed exceptions otherwise. 
     * 
     * @param unknown $schema
     * @throws SettingException
     */
    public static function validate( $schema = null, $tag = '.' )
    {
        if( empty( $schema ) )
        { $schema = self::get(); }
        
        if( ! is_array( $schema ) )
        { throw new SettingException("Element @ $tag must be an array."); }
        
        foreach( $schema as $key => $element )
        {
            if( ! is_array( $element ) )
            { throw new SettingException("Schema error - expecting array type for element $key"); }
            
            if( empty( $element['type'] )) 
            { throw new SettingException("Schema error - missing TYPE field for element $key"); }
            
            switch( $element['type'] )
            {
                case 'group':
                case 'multigroup':
                            self::validateGroup( "$tag.$key", $element );
                            break;
                            
                case 'number':
                case 'enum':
                case 'text':
                            self::validateScalar( "$tag.$key", $element );
                            break;
                            
                default: 
                            throw new SettingException("Element $tag.$key has unknown type ({$element['type']}) expecting group or scalar type");
            }
        }
    }
    
    /**
     * Scalar validation is simply checking that only the proper keys are provided.
     * 
     * @param unknown $tag - jsonpath to the current element.
     * @param unknown $element
     */
    protected static function validateScalar( $tag, $element )
    {
        $valid_keys = ['type', 'sample', 'valid', 'value'];
        
        foreach($element as $key => $value)
        {
            if( ! in_array( $key, $valid_keys ))
            { throw new SettingException("Element $tag has unknown attribute $key"); }
        }
    }
    
    /**
     * Group validation first checks that only the proper keys are provided and then recursively checks
     * each field in the fields element (which is required).
     * 
     * @param unknown $tag
     * @param unknown $element
     */
    protected static function validateGroup( $tag, $element )
    {
        $valid_keys = ['type', 'fields', 'value', 'hidden', 'extensible'];
        
        foreach($element as $key => $value)
        {
            if( ! in_array( $key, $valid_keys ))
            { throw new SettingException("Element $tag has unknown attribute $key"); }
        }
        
        if( empty( $element['fields'] ) )
        { throw new SettingException("Element $tag is missing required 'fields' key."); }
        
        self::validate( $element['fields'], $tag );
    }
}
