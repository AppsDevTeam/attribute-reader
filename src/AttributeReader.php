<?php

namespace Adt\DoctrineLoggable\Mapping\Driver;

use Attribute;
use Doctrine\Common\Annotations\Reader;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use ReflectionMethod;
use BadMethodCallException;

final class AttributeReader implements Reader
{
	/** @var array<string,bool> */
	private array $isRepeatableAttribute = [];

	/**
	 * @return array<object|object[]>
	 * @throws ReflectionException
	 */
	public function getClassAnnotations(ReflectionClass $class): array
	{
		return $this->convertToAttributeInstances($class->getAttributes());
	}

	/**
	 * @phpstan-param class-string $annotationName
	 *
	 * @return object|object[]|null
	 * @throws ReflectionException
	 */
	public function getClassAnnotation(ReflectionClass $class, $annotationName): object|array|null
	{
		return $this->getClassAnnotations($class)[$annotationName] ?? null;
	}

	/**
	 * @return array<object|object[]>
	 * @throws ReflectionException
	 */
	public function getPropertyAnnotations(ReflectionProperty $property): array
	{
		return $this->convertToAttributeInstances($property->getAttributes());
	}

	/**
	 * @phpstan-param class-string $annotationName
	 *
	 * @return object|object[]|null
	 * @throws ReflectionException
	 */
	public function getPropertyAnnotation(ReflectionProperty $property, $annotationName): object|array|null
	{
		return $this->getPropertyAnnotations($property)[$annotationName] ?? null;
	}

	/**
	 * @param array<ReflectionAttribute> $attributes
	 *
	 * @return array<string, object|object[]>
	 * @throws ReflectionException
	 */
	private function convertToAttributeInstances(array $attributes): array
	{
		$instances = [];

		foreach ($attributes as $attribute) {
			$attributeName = $attribute->getName();
			assert(is_string($attributeName));

			$instance = $attribute->newInstance();

			if ($this->isRepeatable($attributeName)) {
				if (!isset($instances[$attributeName])) {
					$instances[$attributeName] = [];
				}

				$instances[$attributeName][] = $instance;
			} else {
				$instances[$attributeName] = $instance;
			}
		}

		return $instances;
	}

	/**
	 * @throws ReflectionException
	 */
	private function isRepeatable(string $attributeClassName): bool
	{
		if (isset($this->isRepeatableAttribute[$attributeClassName])) {
			return $this->isRepeatableAttribute[$attributeClassName];
		}

		$reflectionClass = new ReflectionClass($attributeClassName);
		$attribute = $reflectionClass->getAttributes()[0]->newInstance();

		return $this->isRepeatableAttribute[$attributeClassName] = ($attribute->flags & Attribute::IS_REPEATABLE) > 0;
	}

	public function getMethodAnnotations(ReflectionMethod $method): array
	{
		throw new BadMethodCallException('Not implemented');
	}

	public function getMethodAnnotation(ReflectionMethod $method, $annotationName)
	{
		throw new BadMethodCallException('Not implemented');
	}
}
