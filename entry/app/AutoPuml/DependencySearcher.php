<?php

declare(strict_types=1);

namespace App\AutoPuml;

use Flashcard\Infrastructure\Http\Controllers\v2\SessionController;

/** @TODO Refactor this piece of shit code */
class DependencySearcher
{
    private array $classesToIngore = [];

    public function findRelatedClasses(string $class, int $maxDepth = 3): array
    {
        return $this->scanClass(SessionController::class, $maxDepth, 0);
    }

    private function scanClass(string $class, int $maxDepth, int $currentDepth = 0, $parent = null): array
    {
        if ($currentDepth > $maxDepth) {
            return [];
        }
        if (in_array($class, $this->classesToIngore)) {
            return [];
        }

        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return [];
        }

        $file = $reflection->getFileName();
        if (!$file || str_contains($file, 'vendor')) {
            return [];
        }

        $type = match (true) {
            $reflection->isTrait() => 'trait',
            $reflection->isInterface() => 'interface',
            $reflection->isAbstract() => 'abstract class',
            default => 'class',
        };

        $results = [
            'type' => $type,
            'class' => $class,
            'properties' => [],
            'methods' => [],
            'dependencies' => [],
            'dependents' => [],
        ];

        $dependencies = [];

        foreach ($reflection->getMethods() as $method) {
            $returnType = $method->getReturnType();
            $typeName = $returnType ? ($returnType instanceof \ReflectionNamedType ? $returnType->getName() : (string) $returnType) : null;
            $results['methods'][$method->getName()] = $typeName;

            try {
                if (!$typeName) {
                    continue;
                }
                $depReflection = new \ReflectionClass($typeName);
            } catch (\ReflectionException $e) {
                continue;
            }

            $dependencies[$typeName] = [
                'type' => $typeName,
                'file' => $depReflection->getFileName(),
                'relation' => 'property',
            ];
        }

        foreach ($reflection->getProperties() as $property) {
            $propertyType = $property->getType();
            $typeName = $propertyType ? ($propertyType instanceof \ReflectionNamedType ? $propertyType->getName() : (string) $propertyType) : null;
            $results['properties'][$property->getName()] = $typeName;

            try {
                if (!$typeName) {
                    continue;
                }
                $depReflection = new \ReflectionClass($typeName);
            } catch (\ReflectionException $e) {
                continue;
            }

            $dependencies[$typeName] = [
                'type' => $typeName,
                'file' => $depReflection->getFileName(),
                'relation' => 'return type',
            ];
        }

        $dependencies = array_merge($this->getClassDependencies($class), $dependencies);

        foreach ($dependencies as $dependency => $values) {
            try {
                if (!$dependency) {
                    continue;
                }
                $depReflection = new \ReflectionClass($dependency);
            } catch (\ReflectionException $e) {
                continue;
            }

            $depFile = $depReflection->getFileName();
            if (!$depFile || str_contains($depFile, 'vendor')) {
                continue;
            }

            $temp = $this->scanClass($dependency, $maxDepth, $currentDepth + 1, $class);

            $results['dependencies'][$dependency] = $temp;
            $results['dependencies'][$dependency]['relation'] = $values['relation'] ?? null;
        }

        // Find dependent classes (e.g., implementations, subclasses)
        if ($reflection->isInterface() || ($reflection->isAbstract() && !$reflection->isEnum())) {
            $dependents = $this->findDependentClasses($class, [
                base_path('../Integrations'),
                base_path('../User'),
                base_path('../Flashcard'),
                base_path('../Exercise'),
                base_path('../Shared'),
            ]);
            $dependents = array_filter($dependents, fn ($d) => !in_array($d, $this->classesToIngore));

            foreach ($dependents as $dependent) {
                try {
                    if (!$dependent) {
                        continue;
                    }
                    $depReflection = new \ReflectionClass($dependent);
                } catch (\ReflectionException $e) {
                    continue;
                }

                $depFile = $depReflection->getFileName();
                if (!$depFile || str_contains($depFile, 'vendor')) {
                    continue;
                }

                if ($reflection->isInterface() && !$depReflection->implementsInterface($reflection->getName())) {
                    continue;
                }

                if ($reflection->isAbstract() && !$depReflection->isSubclassOf($reflection->getName())) {
                    continue;
                }
                if ($depReflection->isInterface() && $currentDepth == 0) {
                    continue;
                }

                $results['dependents'][$dependent] = $this->scanClass($dependent, $maxDepth, $currentDepth + 1);
                $results['dependents'][$dependent]['relation'] = 'dependent';
            }
        }

        return $results;
    }

    public function getClassDependencies($className)
    {
        $dependencies = [];
        $importMap = []; // Map to store "short name" => "fully qualified name"

        $targetClass = new \ReflectionClass($className);
        $targetFile = $targetClass->getFileName();
        $targetNamespace = $targetClass->getNamespaceName();

        // Get file content
        $content = file_get_contents($targetFile);

        // Extract use statements and build import map
        preg_match_all('/^\s*use\s+([^;]+);/m', $content, $useMatches);
        foreach ($useMatches[1] as $useStatement) {
            $parts = array_map('trim', explode(',', $useStatement));
            foreach ($parts as $part) {
                if (mb_strpos($part, ' as ') !== false) {
                    list($class, $alias) = explode(' as ', $part);
                    $class = trim($class);
                    $alias = trim($alias);
                    $shortName = $alias;
                } else {
                    $class = trim($part);
                    $segments = explode('\\', $class);
                    $shortName = end($segments);
                }

                $importMap[$shortName] = $class;

                $this->validateAndAddClass($class, $dependencies);
            }
        }

        // Extract class references using regex patterns
        $patterns = [
            // pattern => relation type
            '/\bnew\s+([A-Za-z0-9_\\\]+)/' => 'new', // new class
            '/([A-Za-z0-9_\\\]+)::[A-Za-z0-9_]+/' => 'static call', // static call
            '/\bextends\s+([A-Za-z0-9_\\\]+)/' => 'extends', // extends
            '/\bimplements\s+([^{]+)/' => 'implements', // implements
            '/@var\s+([A-Za-z0-9_\\\|]+)/' => 'type hint', // variable
            '/@param\s+([A-Za-z0-9_\\\|]+)/' => 'type hint', // parameter of function
            '/@return\s+([A-Za-z0-9_\\\|]+)/' => 'return type', // return type
            '/@property\s+([A-Za-z0-9_\\\|]+)/' => 'property', // property
            '/\bfunction\s+\w+\s*\([^)]*\)\s*:\s*([A-Za-z0-9_\\\?]+)/' => 'return type', // function return type
            '/([?]?[a-zA-Z_\\\][a-zA-Z0-9_\\\]*)\s+\$[a-zA-Z_][a-zA-Z0-9_]*/' => 'type hint', // method param
        ];

        foreach ($patterns as $pattern => $relationType) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $types = preg_split('/[\|&]/', str_replace(['?', '[', ']'], '', $match));
                    foreach ($types as $type) {
                        $type = trim($type);
                        if (empty($type) || in_array(mb_strtolower($type), ['string', 'int', 'bool', 'float', 'array', 'object', 'null', 'mixed', 'void', 'callable', 'iterable', 'self', 'parent', 'static'])) {
                            continue;
                        }

                        // Resolve the fully qualified class name
                        $fullyQualifiedName = $this->resolveFullyQualifiedName($type, $importMap, $targetNamespace);

                        // Validate class exists
                        if ($this->validateAndAddClass($fullyQualifiedName, $dependencies, $targetNamespace)) {
                            // Here, we store the relationship type too
                            $dependencies[$fullyQualifiedName] = [
                                'type' => $fullyQualifiedName,
                                'file' => $dependencies[$fullyQualifiedName] ?? null, // original filepath if available
                                'relation' => $relationType,
                            ];
                        }
                    }
                }
            }
        }

        // Handle implements section separately (multiple interfaces)
        preg_match('/\bimplements\s+([^{]+)/', $content, $implementsMatch);
        if (!empty($implementsMatch[1])) {
            $interfaces = array_map('trim', explode(',', $implementsMatch[1]));
            foreach ($interfaces as $interface) {
                $interface = trim($interface);
                $fullyQualifiedInterface = $this->resolveFullyQualifiedName($interface, $importMap, $targetNamespace);

                if ($this->validateAndAddClass($fullyQualifiedInterface, $dependencies, $targetNamespace)) {
                    $dependencies[$fullyQualifiedInterface] = [
                        'type' => $fullyQualifiedInterface,
                        'file' => $dependencies[$fullyQualifiedInterface] ?? null,
                        'relation' => 'implements',
                    ];
                }
            }
        }

        // Remove self from dependencies
        unset($dependencies[$className]);

        return $dependencies;
    }

    private function resolveFullyQualifiedName($className, array $importMap, $currentNamespace)
    {
        // If class name is already fully qualified (starts with \)
        if (mb_strpos($className, '\\') === 0) {
            return ltrim($className, '\\');
        }

        // If class name contains namespace separators
        if (mb_strpos($className, '\\') !== false) {
            // Check if it's a sub-namespace of an imported namespace
            $parts = explode('\\', $className);
            $firstPart = array_shift($parts);

            if (isset($importMap[$firstPart])) {
                return $importMap[$firstPart] . '\\' . implode('\\', $parts);
            }

            // Assume it's relative to current namespace
            return $currentNamespace . '\\' . $className;
        }

        // Simple class name - check if it's in import map
        if (isset($importMap[$className])) {
            return $importMap[$className];
        }

        // If not in import map, assume it's in the current namespace
        return $currentNamespace . '\\' . $className;
    }

    public function validateAndAddClass($class, &$dependencies, $currentNamespace = '')
    {
        if (mb_strpos($class, '\\') !== 0 && mb_strpos($class, '\\') === false && !empty($currentNamespace)) {
            $class = $currentNamespace . '\\' . $class;
        } else {
            $class = ltrim($class, '\\');
        }

        try {
            new \ReflectionClass($class);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function findDependentClasses($targetClassName, $projectDirs, $ignoredDirs = [])
    {
        $dependents = [];
        $targetReflection = new \ReflectionClass($targetClassName);
        $targetShortName = $targetReflection->getShortName();

        // Convert namespace to pattern for regex matching
        $targetNamespacePattern = preg_quote($targetClassName, '/');
        $targetShortNamePattern = preg_quote($targetShortName, '/');

        // Convert single directory to array for consistent handling
        if (!is_array($projectDirs)) {
            $projectDirs = [$projectDirs];
        }

        // Convert single ignored directory to array for consistent handling
        if (!is_array($ignoredDirs)) {
            $ignoredDirs = $ignoredDirs ? [$ignoredDirs] : [];
        }

        // Normalize ignored directories to absolute paths
        $normalizedIgnoredDirs = [];
        foreach ($ignoredDirs as $dir) {
            $normalizedIgnoredDirs[] = rtrim(realpath($dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        // Process each directory
        foreach ($projectDirs as $projectDir) {
            // Get all PHP files in this project directory
            $directory = new \RecursiveDirectoryIterator($projectDir);
            $iterator = new \RecursiveIteratorIterator($directory);
            $phpFiles = new \RegexIterator($iterator, '/\.php$/i');

            foreach ($phpFiles as $phpFile) {
                $filePath = $phpFile->getPathname();
                $realFilePath = realpath($filePath);

                // Skip if file is in an ignored directory
                $shouldSkip = false;
                foreach ($normalizedIgnoredDirs as $ignoredDir) {
                    if (mb_strpos($realFilePath, $ignoredDir) === 0) {
                        $shouldSkip = true;

                        break;
                    }
                }

                if ($shouldSkip) {
                    continue;
                }

                $content = file_get_contents($filePath);
                $classFound = false;

                // Check for fully qualified class name usage
                if (preg_match('/[\\\]' . $targetNamespacePattern . '\b/', $content)
                    || preg_match('/use\s+' . $targetNamespacePattern . '(\s*;|\s+as)/', $content)) {
                    $classFound = true;
                }

                // Check for class short name with proper use statement
                if (preg_match('/\b' . $targetShortNamePattern . '\b/', $content)) {
                    $namespaceBase = preg_quote(str_replace('\\' . $targetShortName, '', $targetClassName), '/');
                    if (!empty($namespaceBase) && preg_match('/use\s+' . $namespaceBase . '(\\\\\*|\b)/', $content)) {
                        $classFound = true;
                    }
                }

                // Check for extends, implements, new, ::, type hints
                $patterns = [
                    '/\bextends\s+' . $targetShortNamePattern . '\b/',
                    '/\bimplements[^{]+(,\s*|\s+)' . $targetShortNamePattern . '\b/',
                    '/\bnew\s+' . $targetShortNamePattern . '\b/',
                    '/\b' . $targetShortNamePattern . '::[A-Za-z0-9_]+\b/',
                    '/@var\s+' . $targetShortNamePattern . '\b/',
                    '/@param\s+' . $targetShortNamePattern . '\b/',
                    '/@return\s+' . $targetShortNamePattern . '\b/',
                    '/function\s+\w+\s*\([^)]*\)\s*:\s*' . $targetShortNamePattern . '\b/',
                    '/:\s*' . $targetShortNamePattern . '\s*\$/',
                    '/\$\w+\s*=\s*' . $targetShortNamePattern . '::/',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        $classFound = true;

                        break;
                    }
                }

                if ($classFound) {
                    $fileNamespace = $this->extractNamespace($content);
                    $fileClasses = $this->extractClasses($content, $fileNamespace);

                    // Add all classes from this file to dependents
                    foreach ($fileClasses as $className => $classType) {
                        // Skip if this is the target class itself
                        if ($className === $targetClassName) {
                            continue;
                        }

                        $dependents[] = $className;
                    }
                }
            }
        }

        return $dependents;
    }

    public function extractNamespace($content)
    {
        $namespace = '';
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
        }

        return $namespace;
    }

    public function extractClasses($content, $namespace)
    {
        $classes = [];

        // Extract all class, interface, and trait definitions
        $patterns = [
            '/\bclass\s+([A-Za-z0-9_]+)/' => 'class',
            '/\binterface\s+([A-Za-z0-9_]+)/' => 'interface',
            '/\btrait\s+([A-Za-z0-9_]+)/' => 'trait',
        ];

        foreach ($patterns as $pattern => $type) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $className) {
                    $fullClassName = !empty($namespace) ? $namespace . '\\' . $className : $className;
                    $classes[$fullClassName] = $type;
                }
            }
        }

        return $classes;
    }
}
