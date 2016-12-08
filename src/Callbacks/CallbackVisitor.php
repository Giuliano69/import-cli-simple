<?php

/**
 * TechDivision\Import\Cli\Callbacks\CallbackVisitor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli\Callbacks;

use TechDivision\Import\Subjects\SubjectInterface;

/**
 * Visitor implementation for a subject's callbacks.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class CallbackVisitor
{

    /**
     * Return's a new visitor instance.
     *
     * @return \TechDivision\Import\Cli\Callbacks\CallbackVisitor The visitor instance
     */
    public static function get()
    {
        return new CallbackVisitor();
    }

    /**
     * Visitor implementation that initializes the observers of the passed subject.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject to initialize the observers for
     *
     * @return void
     */
    public function visit(SubjectInterface $subject)
    {
        // prepare the callbacks
        foreach ($subject->getConfiguration()->getCallbacks() as $callbacks) {
            $this->prepareCallbacks($subject, $callbacks);
        }
    }

    /**
     * Prepare the callbacks defined in the system configuration.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject   The subject to prepare the callbacks for
     * @param array                                          $callbacks The array with the callbacks
     * @param string                                         $type      The actual callback type
     *
     * @return void
     */
    public function prepareCallbacks(SubjectInterface $subject, array $callbacks, $type = null)
    {

        // iterate over the array with callbacks and prepare them
        foreach ($callbacks as $key => $callback) {
            // we have to initialize the type only on the first level
            if ($type == null) {
                $type = $key;
            }

            // query whether or not we've an subarry or not
            if (is_array($callback)) {
                $this->prepareCallbacks($subject, $callback, $type);
            } else {
                $callbackInstance = $this->callbackFactory($subject, $callback);
                $subject->registerCallback($callbackInstance, $type);
            }
        }
    }

    /**
     * Initialize and return a new callback of the passed type.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject   The subject to create the observer for
     * @param string                                         $className The type of the callback to instanciate
     *
     * @return \TechDivision\Import\Callbacks\CallbackInterface The callback instance
     */
    public function callbackFactory(SubjectInterface $subject, $className)
    {
        return new $className($subject);
    }
}
