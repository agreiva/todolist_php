<?php
// index.php
/**
 * File ini adalah titik awal dari aplikasi.
 *
 * Ia menggunakan kelas TodoController untuk menangani berbagai aksi
 * dan kemudian merender tampilan dengan daftar tugas.
 */

// Memanggil file TodoController.php untuk menggunakan class TodoController
require_once 'controllers/TodoController.php';

// Membuat instance controller
$controller = new TodoController();

// Mendapatkan parameter action dari URL
$action = $_GET['action'] ?? null;

// Menangani parameter aksi
switch ($action) {
    case 'add':
        // Dapatkan tugas dari request
        $task = $_POST['task'] ?? '';
        $deadline = $_POST['deadline'] ?? null;
        // Tambahkan tugas ke daftar
        if ($controller->add($task, $deadline)) {
            // Redirect untuk mencegah form resubmission
            header('Location: index.php?success=add');
            exit;
        }
        break;
        
    case 'complete':
        // Dapatkan id dari request
        $id = $_GET['id'] ?? '';
        // Tandai tugas sebagai selesai
        if ($controller->markAsCompleted($id)) {
            header('Location: index.php?success=complete');
            exit;
        }
        break;
        
    case 'delete':
        // Dapatkan id dari request
        $id = $_GET['id'] ?? '';
        // Hapus tugas dari daftar
        if ($controller->delete($id)) {
            header('Location: index.php?success=delete');
            exit;
        }
        break;
}

// Dapatkan daftar tugas
$todos = $controller->index();

// Merender tampilan
require 'views/listTodos.php';
?>