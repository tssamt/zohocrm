<?php

/*
 * This file is part of mctekk/zohocrm library.
 *
 * (c) MCTekK S.R.L. https://mctekk.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zoho\CRM\Wrapper;

/**
 * Element class for extends Entities
 *
 * @version 1.0.0
 */
abstract class Element
{
    /**
     * Getter
     *
     * @param mixed $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return isset($this->$property) ? $this->$property : null;
    }

    /**
     * Setter
     *
     * @param string $property Name of the property to set the value
     * @param mixed $value Value for the property
     *
     * @return mixed
     */
    public function __set($property, $value)
    {
        $this->$property = $value;

        return $this->$property;
    }

    /**
     * The deserialize method is called during xml parsing,
     * create an object of the xml received based on the entity
     * called
     *
     * @param string $xmlstr XML string to convert on object
     *
     * @throws Exception If xml data could not be parsed
     *
     * @return bool
     */
    final public function deserializeXml($xmlstr)
    {
        try {
            $element = new \SimpleXMLElement($xmlstr);
        } catch (\Exception $ex) {
            return false;
        }
        foreach ($element as $name => $value) {
            $this->$name = stripslashes(urldecode(htmlspecialchars_decode($value)));
        }

        return true;
    }

    /**
     * Called during array to xml parsing, create an string
     * of the xml to send for api based on the request values, for sustitution
     * of specials chars use E prefix instead of % for hexadecimal
     *
     * @param array $fields Fields to convert
     *
     * @return string
     *
     * @todo
     * - Verify if the property exist on entity before send to zoho
     */
    final public function serializeXml(array $fields)
    {
        $className = $this->stripNamespaceFromClassName($this);
        $output = '<'.$className.'>';
        foreach ($fields as $key => $value) {
            if (empty($value)) {
                continue;
            }
            // Unnecessary fields
            $key = str_replace(' ', '_', ucwords(str_replace(['_', '$', '%5F', '?'], [' ', 'N36', 'E5F', '98T'], $key)));
            $output .= '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>';
        }
        $output .= '</'.$className.'>';

        return $output;
    }

    /**
     * Strip the namespace from the class to get the actual class name
     *
     * @param object $obj
     *
     * @return string
     */
    private function stripNamespaceFromClassName($obj)
    {
        $className = get_class($obj);

        return mb_substr($className, mb_strrpos($className, '\\') + 1);
    }
}
