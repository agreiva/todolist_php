<?php
// api.php
/**
 * REST API Endpoint untuk Todo List
 * Mendukung operasi CRUD melalui HTTP methods
 */

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include controller
require_once 'controllers/TodoController.php';

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Ambil parameter action dari URL
$action = $_GET['action'] ?? null;

// Buat instance controller
$controller = new TodoController();

// Handle berdasarkan action
switch ($action) {
    case 'list':
        // GET: Mengambil semua todos
        try {
            $todos = $controller->index();
            echo json_encode([
                'success' => true,
                'data' => $todos,
                'total' => count($todos)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching todos: ' . $e->getMessage()
            ]);
        }
        break;

    case 'add':
        // POST: Menambahkan todo baru
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari JSON body
            $inputData = json_decode(file_get_contents('php://input'), true);
            
            if (isset($inputData['task']) && !empty(trim($inputData['task']))) {
                $task = trim($inputData['task']);
                $deadline = $inputData['deadline'] ?? null;
                
                try {
                    if ($controller->add($task, $deadline)) {
                        http_response_code(201);
                        echo json_encode([
                            'success' => true,
                            'message' => 'Todo berhasil ditambahkan'
                        ]);
                    } else {
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Gagal menambahkan todo'
                        ]);
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Task tidak boleh kosong'
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak valid. Gunakan POST untuk menambahkan todo'
            ]);
        }
        break;

    case 'complete':
        // PUT/POST: Menandai todo sebagai selesai
        $inputData = json_decode(file_get_contents('php://input'), true);
        
        if (isset($inputData['id']) && !empty($inputData['id'])) {
            try {
                if ($controller->markAsCompleted($inputData['id'])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Todo berhasil ditandai selesai'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Gagal menandai todo sebagai selesai'
                    ]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID todo diperlukan'
            ]);
        }
        break;

    case 'delete':
        // DELETE/POST: Menghapus todo
        $inputData = json_decode(file_get_contents('php://input'), true);
        
        if (isset($inputData['id']) && !empty($inputData['id'])) {
            try {
                if ($controller->delete($inputData['id'])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Todo berhasil dihapus'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Gagal menghapus todo'
                    ]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID todo diperlukan'
            ]);
        }
        break;

    case 'get':
        // GET: Mengambil detail satu todo berdasarkan ID
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $todos = $controller->index();
                $todo = array_filter($todos, function($item) {
                    return $item['id'] == $_GET['id'];
                });
                
                if (!empty($todo)) {
                    echo json_encode([
                        'success' => true,
                        'data' => array_values($todo)[0]
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Todo tidak ditemukan'
                    ]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID todo diperlukan'
            ]);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Action tidak valid',
            'available_actions' => [
                'list' => 'GET - Mengambil semua todos',
                'get' => 'GET - Mengambil satu todo (butuh parameter id)',
                'add' => 'POST - Menambahkan todo baru',
                'complete' => 'PUT/POST - Menandai todo selesai',
                'delete' => 'DELETE/POST - Menghapus todo'
            ]
        ]);
}
?>