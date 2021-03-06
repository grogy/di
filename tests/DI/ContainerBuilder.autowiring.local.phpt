<?php

/**
 * Test: Nette\DI\ContainerBuilder and local autowiring.
 */

use Nette\DI;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class Foo
{
	public $arg;

	function test(M $arg)
	{
		$this->arg = $arg;
	}
}


class M
{
}


class M1 extends M
{
}


class M2 extends M
{
}


$builder = new DI\ContainerBuilder;
$builder->addDefinition('foo')
	->setClass('Foo');

$builder->addDefinition('m1')
	->setClass('M1')
	->addSetup('@foo::test');

$builder->addDefinition('m2')
	->setClass('M2')
	->addSetup('@foo::test')
	->setAutowired(FALSE);


$container = createContainer($builder);

$foo = $container->getService('foo');
Assert::type(Foo::class, $foo);
Assert::null($foo->arg);

Assert::type(M1::class, $container->getService('m1'));
Assert::same($foo->arg, $container->getService('m1'));

Assert::type(M2::class, $container->getService('m2'));
Assert::same($foo->arg, $container->getService('m2'));
