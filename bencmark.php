<?php
/**
 * УЧЕБНЫЙ ПРИМЕР: Оптимизация поиска в массиве
 * 
 * ЗАДАЧА:
 * У нас есть список из 50 000 "товаров" (чисел).
 * Нам нужно найти конкретный товар и проверить, есть ли он в списке.
 * 
 * ВНИМАНИЕ: Код ниже написан специально МЕДЛЕННО.
 * Ваша задача - ускорить его.
 */

// ---------------------------------------------------------
// 1. ГЕНЕРАЦИЯ ДАННЫХ 
// ---------------------------------------------------------
function generateProducts(int $count): array {
    $products = [];
    for ($i = 0; $i < $count; $i++) {
        $products[] = [
            'id' => $i,
            'name' => 'Product_' . $i,
            'price' => rand(100, 10000)
        ];
    }
    return $products;
}

// ---------------------------------------------------------
// ВАРИАНТ 1: МЕДЛЕННЫЙ КОД (линейный поиск)
// ---------------------------------------------------------
function searchProductSlow($products, $searchId) {
    foreach ($products as $product) {
        if ($product['id'] == $searchId) {
            return $product;
        }
    }
    return null;
}

// ---------------------------------------------------------
// ВАРИАНТ 2: БЫСТРЫЙ КОД (оптимизация)
// ---------------------------------------------------------
function searchProductFast($products, $searchId) {
    $assoc = [];
    foreach ($products as $product) {
        $assoc[$product['id']] = $product;
    }
    return $assoc[$searchId] ?? null;
}

// ---------------------------------------------------------
// 3. ФУНКЦИЯ БЕНЧМАРКА
// ---------------------------------------------------------
function runBenchmark(callable $function, array $data, int $searchId, int $iterations = 100): array {
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $function($data, $searchId);
    }
    $finish = microtime(true);
    
    $totalTime = $finish - $start;
    $avgTimeMs = ($totalTime / $iterations) * 1000; // в миллисекундах
    
    return [
        'total_time_sec' => round($totalTime, 6),
        'avg_time_ms'    => round($avgTimeMs, 6),
        'iterations'     => $iterations
    ];
}

// ---------------------------------------------------------
// 4. ЗАПУСК ТЕСТОВ
// ---------------------------------------------------------

$productCount = 50000;
$products = generateProducts($productCount);

$searchId = 25000; 
$iterations = 1000; 

echo "=== РЕЗУЛЬТАТЫ БЕНЧМАРКА ({$iterations} итераций, поиск ID={$searchId}) ===" . PHP_EOL . PHP_EOL;

$slowResult = runBenchmark('searchProductSlow', $products, $searchId, $iterations);
echo "Тест: searchProductSlow" . PHP_EOL;
echo "  Общее время: " . $slowResult['total_time_sec'] . " сек" . PHP_EOL;
echo "  Среднее на вызов: " . $slowResult['avg_time_ms'] . " мс" . PHP_EOL . PHP_EOL;

$fastResult = runBenchmark('searchProductFast', $products, $searchId, $iterations);
echo "Тест: searchProductFast" . PHP_EOL;
echo "  Общее время: " . $fastResult['total_time_sec'] . " сек" . PHP_EOL;
echo "  Среднее на вызов: " . $fastResult['avg_time_ms'] . " мс" . PHP_EOL . PHP_EOL;

if ($fastResult['total_time_sec'] > 0) {
    $speedup = round($slowResult['total_time_sec'] / $fastResult['total_time_sec'], 2);
} else {
    $speedup = '∞';
}
echo ">>> Ускорение: {$speedup} раз" . PHP_EOL;
