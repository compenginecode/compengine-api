<?php

namespace PresentationLayer\Routing\Plugins\SchemaValidationPlugin;

use Json\Validator;

class SchemaValidationPlugin{

    /** validate
     *
     *  Validates the given object against the schema file.
     *
     * @param $schemaFile
     * @param $object
     */
    private static function validate($schemaFile, $object){
        $validator = new Validator($schemaFile);
        $validator->validate($object);
    }

    /** validateAgainstSchema
     *
     *  This function will obtain the filename that the class $classPtr is defined in, and
     *  look for a file $filename in the same directory. If it exists, it will load it,
     *  assume it's a JSON schema validation file, and validate the $params object
     *  against it.
     *
     *  Throws Json\ValidationException when errors in validation occur.
     *
     * @param $classPtr
     * @param array $params
     * @param string $filename
     * @throws EInvalidInput
     * @throws ESchemaFileMissing
     * @throws \Json\ValidationException
     */
    public static function validateAgainstSchema($classPtr, array $params, $filename = "schema.json"){
        if (!is_array($classPtr)){
            $reflector = new \ReflectionClass($classPtr);
            $directory = rtrim(dirname($reflector->getFileName()), "/") . "/";
            $path = $directory . $filename;

            if (file_exists($path)){
                /** We convert the array to an object */
                $object = json_decode(json_encode($params));
                self::validate($path, $object);
            }else{
                throw new ESchemaFileMissing($filename, $directory);
            }
        }else{
            throw new EInvalidInput();
        }
    }
}