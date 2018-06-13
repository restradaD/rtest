<?php
/**
 * @author  Almog Baku
 *          almog.baku@gmail.com
 *          http://www.AlmogBaku.com
 *
 * 25/06/15 15:28
 */

namespace AppBundle\Serializer;

use \JMS\Serializer\JsonSerializationVisitor as JsonSerializationVisitorBase;

class JsonSerialization extends JsonSerializationVisitorBase
{
    public function getResult()
    {
        if($this->getRoot() instanceof \ArrayObject) {
            $this->setRoot((array) $this->getRoot());
        }
        return parent::getResult();
    }
}