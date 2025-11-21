<!-- views/listTodos.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Todo List</h1>
        
        <form method="POST" action="?action=add" class="add-form">
            <div class="form-group">
                <label>Tugas</label>
                <input type="text" name="task" placeholder="Masukkan tugas baru..." required>
            </div>
            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline">
            </div>
            <button type="submit">Tambah Tugas</button>
        </form>

        <div class="todo-container">
            <?php if (empty($todos)): ?>
                <p class="empty-state">Belum ada tugas.</p>
            <?php else: ?>
                <?php foreach ($todos as $todo): ?>
                    <?php
                    $status_class = '';
                    $status_text = '';
                    
                    if (!empty($todo['deadline']) && !$todo['is_completed']) {
                        $today = new DateTime();
                        $deadline_date = new DateTime($todo['deadline']);
                        $diff = $today->diff($deadline_date);
                        $days_left = (int)$diff->format('%r%a');
                        
                        if ($days_left < 0) {
                            $status_class = 'overdue';
                            $status_text = 'Terlambat';
                        } elseif ($days_left == 0) {
                            $status_class = 'today';
                            $status_text = 'Hari ini';
                        } elseif ($days_left == 1) {
                            $status_class = 'urgent';
                            $status_text = 'Besok';
                        } elseif ($days_left <= 3) {
                            $status_class = 'soon';
                            $status_text = $days_left . ' hari lagi';
                        }
                    }
                    ?>
                    
                    <div class="todo-item <?php echo $todo['is_completed'] ? 'completed' : ''; ?> <?php echo $status_class; ?>">
                        <div class="todo-main">
                            <div class="todo-info">
                                <h3 class="todo-title"><?php echo htmlspecialchars($todo['task']); ?></h3>
                                <div class="todo-meta">
                                    <span class="meta-item">Dibuat: <?php echo date('d M Y', strtotime($todo['created_at'])); ?></span>
                                    <?php if (!empty($todo['deadline'])): ?>
                                        <span class="meta-item deadline-info">
                                            Deadline: <?php echo date('d M Y', strtotime($todo['deadline'])); ?>
                                            <?php if (!empty($status_text) && !$todo['is_completed']): ?>
                                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="meta-item no-deadline">Deadline: Tidak ada</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="todo-actions">
                            <?php if (!$todo['is_completed']): ?>
                                <a href="?action=complete&id=<?php echo $todo['id']; ?>" class="btn btn-complete" title="Selesai">Selesai</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $todo['id']; ?>" class="btn btn-delete" onclick="return confirm('Hapus tugas ini?')" title="Hapus">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Total: <?php echo count($todos); ?> tugas</p>
        </div>
    </div>
</body>
</html>