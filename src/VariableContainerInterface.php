<?php
/*
 * This file is part of the Organizer package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\Organizer;

interface VariableContainerInterface
{
    /**
     * Initialize dynamic variables (in bulk).
     */
    public function vars(array $variables);

    /**
     * Sets a value of dynamic variable.
     */
    public function setVar($key, $value);
}
