<?php
header('Content-Type: application/json');

try {
    $db = new SQLite3(__DIR__ . "/store.db");
} catch (Exception $e) {
    echo json_encode(['error' => 'Error de connexió a la base de dades: ' . $e->getMessage()]);
    exit;
}

// Función para obtener un producto por ID
function getProductById($db, $id) {
    $stmt = $db->prepare('SELECT * FROM productes WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

// Función para obtener todas las categorías
function getAllCategories($db) {
    $result = $db->query('SELECT DISTINCT categoria FROM productes');
    $categories = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $categories[] = $row['categoria'];
    }
    return $categories;
}

// Función para obtener productos por categoría
function getProductsByCategory($db, $category) {
    $stmt = $db->prepare('SELECT * FROM productes WHERE categoria = :category');
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $result = $stmt->execute();
    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    return $products;
}

// Función para obtener todos los productos
function getAllProducts($db) {
    $result = $db->query('SELECT * FROM productes');
    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    return $products;
}

try {
    // Obtener producto por ID
    if (isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            echo json_encode(['error' => 'ID no válido']);
            exit;
        }

        $product = getProductById($db, $id);
        if ($product) {
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Producto no encontrado']);
        }
        exit;
    }

    // Obtener todas las categorías
    if (isset($_GET['categories']) && $_GET['categories'] === 'all') {
        $categories = getAllCategories($db);
        echo json_encode($categories);
        exit;
    }

    // Obtener productos por categoría
    if (isset($_GET['category'])) {
        $category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
        $products = getProductsByCategory($db, $category);
        echo json_encode($products);
        exit;
    }

    // Obtener todos los productos
    $products = getAllProducts($db);
    echo json_encode($products);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error de base de dades: ' . $e->getMessage()]);
    exit;
}
?>