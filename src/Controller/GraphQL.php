<?php

namespace App\Controller;
require_once __DIR__ . '/../../vendor/autoload.php';
$modelsDirectory = __DIR__ . '/../Model/';
$arrayOfTypeFiles = glob($modelsDirectory . '*.php');
foreach ($arrayOfTypeFiles as $filename) {
    require_once $filename;
}

$typesDirectory = __DIR__ . '/../Type/';
$arrayOfTypeFiles = glob($typesDirectory . '*.php');
foreach ($arrayOfTypeFiles as $filename) {
    require_once $filename;
}

use Category;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use App\GraphQL\Types\CategoryType;


class GraphQL {

    static public function handle() {
        try {

            $categoriesModel = new Category(); 
            $categoryType = new CategoryType();  
 error_log("Category model instantiated");

             $queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'GetCategories' => [
            'type' => Type::listOf($categoryType),
            'resolve' => function() use ($categoriesModel) {
                    error_log("GetCategories resolver started");

                    // First, try to use the real database
                    $realCategories = $categoriesModel->getCategories();
                    error_log("Real categories fetched: " . print_r($realCategories, true));

                    // If real categories are empty, fall back to our test function
                    if (empty($realCategories)) {
                        error_log("Using test function since real categories are empty");
                        $categories = $categoriesModel->testFunction();
                    } else {
                        $categories = $realCategories;
                    }

                    // Check if categories were fetched successfully
                    if (empty($categories)) {
                        error_log("No categories found (either real or test)");
                        return [];
                    }

                    // Format the result
                    $formattedCategories = array_map(function($category) {
                        return [
                            'id' => $category['id'],
                            'name' => $category['name']
                        ];
                    }, $categories);

                    error_log("Formatted categories: " . print_r($formattedCategories, true));
                    return $formattedCategories;
                },
                ],
                ],
            ]);
        
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                ],
            ]);
        
            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}