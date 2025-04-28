<?php

declare(strict_types=1);

namespace App\AutoPuml;

class PlantUmlConverter
{
    private array $processedClasses = [];
    private array $plantUmlRelations = [];
    private array $plantUmlDefinitions = [];

    public function convert(array $structure): string
    {
        // Process the main class and its dependencies recursively
        $this->processClass($structure);

        // Build the PlantUML diagram
        return $this->buildPlantUml();
    }

    private function processClass(array $classInfo, ?string $parentClass = null, ?string $relation = null): void
    {
        $className = $classInfo['class'] ?? '';

        // Skip if we've already processed this class
        if (in_array($className, $this->processedClasses)) {
            // Still add relation if it exists
            if ($parentClass && $relation) {
                $this->addRelation($parentClass, $className, $relation);
            }

            return;
        }

        // Add to processed classes
        $this->processedClasses[] = $className;

        // Add relation to parent if exists
        if ($parentClass && $relation) {
            $this->addRelation($parentClass, $className, $relation);
        }

        // Process class definition
        $this->addClassDefinition($classInfo);

        // Process dependencies
        if (isset($classInfo['dependencies']) && is_array($classInfo['dependencies'])) {
            foreach ($classInfo['dependencies'] as $dependencyName => $dependencyInfo) {
                if (is_array($dependencyInfo)) {
                    $relationType = $dependencyInfo['relation'] ?? 'type hint';

                    // If the dependency info is a complete class definition
                    if (isset($dependencyInfo['class'])) {
                        $this->processClass($dependencyInfo, $className, $relationType);
                    } else {
                        // It's just a relation reference
                        $this->addRelation($className, $dependencyName, $relationType);
                    }
                }
            }
        }

        // Process dependents
        if (isset($classInfo['dependents']) && is_array($classInfo['dependents'])) {
            foreach ($classInfo['dependents'] as $dependentName => $dependentInfo) {
                if (is_array($dependentInfo)) {
                    $relationType = $dependentInfo['relation'] ?? 'dependent';

                    // If the dependent info is a complete class definition
                    if (isset($dependentInfo['class'])) {
                        $this->processClass($dependentInfo, $className, $relationType);
                    } else {
                        // It's just a relation reference
                        $this->addRelation($className, $dependentName, $relationType);
                    }
                }
            }
        }
    }

    private function addClassDefinition(array $classInfo): void
    {
        $className = $classInfo['class'] ?? '';
        if (empty($className)) {
            return;
        }

        $type = $classInfo['type'] ?? 'class';
        $properties = $classInfo['properties'] ?? [];
        $methods = $classInfo['methods'] ?? [];

        $definition = '';

        // Define the class with appropriate stereotype
        switch ($type) {
            case 'interface':
                $definition .= "interface {$className} {\n";

                break;

            case 'abstract':
                $definition .= "abstract class {$className} {\n";

                break;

            case 'trait':
                $definition .= "class {$className} << (T,orchid) trait >> {\n";

                break;

            default:
                $definition .= "class {$className} {\n";

                break;
        }

        // Add properties
        foreach ($properties as $propName => $propType) {
            $definition .= "  +{$propName}: {$propType}\n";
        }

        // Add a separator if we have both properties and methods
        if (!empty($properties) && !empty($methods)) {
            $definition .= "  --\n";
        }

        // Add methods
        foreach ($methods as $methodName => $returnType) {
            $returnTypeStr = $returnType ? ": {$returnType}" : '';
            $definition .= "  +{$methodName}(){$returnTypeStr}\n";
        }

        $definition .= "}\n";

        $this->plantUmlDefinitions[] = $definition;
    }

    private function addRelation(string $source, string $target, string $relationType): void
    {
        $relation = '';

        switch ($relationType) {
            case 'extends':
                $relation = "{$target} <|-- {$source}";

                break;

            case 'implements':
                $relation = "{$target} <|.. {$source}";

                break;

            case 'dependent':
                $relation = "{$target} --> {$source}";

                break;

            case 'property':
                $relation = "{$source} --> {$target}";

                break;

            case 'return type':
                $relation = "{$source} o-- {$target}";

                break;

            case 'new':
                $relation = "{$source} ..> {$target} : <<create>>";

                break;

            case 'static call':
                $relation = "{$source} ..> {$target} : <<static>>";

                break;

            case 'type hint':
                $relation = "{$source} ..> {$target}";

                break;

            default:
                $relation = "{$source} --> {$target}";

                break;
        }

        $this->plantUmlRelations[] = $relation;
    }

    private function buildPlantUml(): string
    {
        $output = "@startuml\n";
        $output .= "skinparam classAttributeIconSize 0\n\n";

        // Add class definitions
        foreach ($this->plantUmlDefinitions as $definition) {
            $output .= $definition . "\n";
        }

        // Add relationships
        foreach ($this->plantUmlRelations as $relation) {
            $output .= $relation . "\n";
        }

        $output .= '@enduml';

        return $output;
    }
}
