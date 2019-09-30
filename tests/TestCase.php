<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    private function getMethod(string $className, string $methodName)
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }
    
    protected function invokeStaticMethod(string $className, string $methodName, array $args = [])
    {
        $method = $this->getMethod($className, $methodName);
        return $method->invokeArgs(null, $args);
    }
    
	protected function invokeMethod(object $object, string $methodName, array $args = [])
	{
		$className = get_class($object);
		$method = $this->getMethod($className, $methodName);
		return $method->invokeArgs($object, $args);
	}
}
