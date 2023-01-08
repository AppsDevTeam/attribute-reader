<?php

namespace ADT\AttributeReader;

use BadMethodCallException;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

final class AttributeAnnotationReader implements Reader
{
    private ?Reader $annotationReader;

    private AttributeReader $attributeReader;

    public function __construct(?Reader $annotationReader = null)
    {
        $this->attributeReader = new AttributeReader();
        $this->annotationReader = $annotationReader;
    }

	/**
	 * @return object[]
	 * @throws ReflectionException
	 */
    public function getClassAnnotations(ReflectionClass $class): array
    {
        $annotations = $this->attributeReader->getClassAnnotations($class);

        if ([] !== $annotations) {
            return $annotations;
        }

		if (!$this->annotationReader) {
			return [];
		}

        return $this->annotationReader->getClassAnnotations($class);
    }

	/**
	 * @param class-string<T> $annotationName the name of the annotation
	 *
	 * @return T|null the Annotation or NULL, if the requested annotation does not exist
	 *
	 * @template T
	 * @throws ReflectionException
	 */
    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        $annotation = $this->attributeReader->getClassAnnotation($class, $annotationName);

        if (null !== $annotation) {
            return $annotation;
        }

		return $this->annotationReader?->getClassAnnotation($class, $annotationName);

	}

	/**
	 * @return object[]
	 * @throws ReflectionException
	 */
    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        $propertyAnnotations = $this->attributeReader->getPropertyAnnotations($property);

        if ([] !== $propertyAnnotations) {
            return $propertyAnnotations;
        }

		if (!$this->annotationReader) {
			return [];
		}

        return $this->annotationReader->getPropertyAnnotations($property);
    }

	/**
	 * @param class-string<T> $annotationName the name of the annotation
	 *
	 * @return T|null the Annotation or NULL, if the requested annotation does not exist
	 *
	 * @template T
	 * @throws ReflectionException
	 */
    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName)
    {
        $annotation = $this->attributeReader->getPropertyAnnotation($property, $annotationName);

        if (null !== $annotation) {
            return $annotation;
        }

		return $this->annotationReader?->getPropertyAnnotation($property, $annotationName);

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
