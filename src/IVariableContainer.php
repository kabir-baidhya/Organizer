<?php
namespace Gckabir\Organizer;

interface IVariableContainer
{
    /**
     * Initialize dynamic variables (in bulk)
     */
    public function vars(array $variables);

    /**
     * Sets a value of dynamic variable
     */
    public function setVar($key, $value);
}
