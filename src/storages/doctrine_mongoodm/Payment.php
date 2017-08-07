<?php
namespace Acme\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Payum\Core\Model\Payment as BasePayment;
/**
 * @Mongo\Document
 */
class Payment extends BasePayment
{
    /**
     * @Mongo\Id
     *
     * @var integer $id
     */
    protected $id;
}
